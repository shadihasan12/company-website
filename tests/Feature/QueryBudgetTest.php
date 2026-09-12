<?php

namespace Tests\Feature;

use App\Models\Project;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Guards against query regressions — both N+1s and the quieter problem of
 * the same rows being fetched two or three times because the header, the
 * footer and the page all ask for them independently.
 */
class QueryBudgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * @return list<array{string, int}>
     */
    public static function pages(): array
    {
        return [
            'home' => ['/en', 20],
            'services index' => ['/en/services', 8],
            'service detail' => ['/en/services/mobile-apps', 14],
            'work index' => ['/en/work', 15],
            'case study' => ['/en/work/wakil-topup', 14],
            'about' => ['/en/about', 10],
            'insights' => ['/en/insights', 10],
            'contact' => ['/en/contact', 8],
        ];
    }

    #[DataProvider('pages')]
    public function test_a_page_stays_within_its_query_budget(string $path, int $budget): void
    {
        $queries = $this->recordQueries($path);

        $this->assertLessThanOrEqual(
            $budget,
            count($queries),
            "[{$path}] ran ".count($queries)." queries, budget {$budget}:\n".implode("\n", $queries),
        );
    }

    #[DataProvider('pages')]
    public function test_a_page_never_runs_the_same_query_twice(string $path): void
    {
        $duplicates = collect($this->recordQueries($path))
            ->countBy()
            ->filter(fn (int $count) => $count > 1);

        $this->assertTrue(
            $duplicates->isEmpty(),
            "[{$path}] repeats queries:\n".$duplicates->map(
                fn (int $count, string $sql) => "  x{$count}  {$sql}",
            )->implode("\n"),
        );
    }

    public function test_adding_case_studies_does_not_add_queries(): void
    {
        // The listing eager-loads its relations; without that this would
        // grow with the number of rows.
        $before = count($this->recordQueries('/en/work'));

        Project::factory()->count(5)->create();

        $this->assertSame($before, count($this->recordQueries('/en/work')));
    }

    /**
     * @return list<string>
     */
    protected function recordQueries(string $path): array
    {
        $queries = [];

        DB::flushQueryLog();
        DB::listen(function ($query) use (&$queries) {
            $sql = preg_replace('/\s+/', ' ', $query->sql);

            // Session writes are infrastructure, not page behaviour.
            if (! str_contains($sql, '"sessions"')) {
                $queries[] = $sql;
            }
        });

        $this->get($path)->assertOk();

        return $queries;
    }
}

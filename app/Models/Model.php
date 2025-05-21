<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Model extends EloquentModel
{
    use HasFactory;

    /**
     * Itens per page
     */
    public static $perpage = 25;

    /**
     * Disable timestamp
     */
    public $timestamps = true;

    /**
     * Define primary key field
     */
    protected $primaryKey = 'id';

    /**
     * Get Columns table
     *
     * @param string $pTable - Table name
     * @return array
     */
    protected static function getColumns(string $pTable): array
    {
        $fetchRows = self::query()
        ->select('column_name')
        ->from('information_schema.columns')
        ->where('table_name', $pTable)
        ->get();

        if (! $fetchRows) {
            return [];
        }

        $columns = [];
        foreach ($fetchRows->toArray() as $fetchRow) {
            $columns[] = $fetchRow['column_name'];
        }

        return array_map('strtolower', $columns);
    }

    /**
     * Scope Filters
     *
     * @param Builder $pQuery - Object Query Builder.
     * @param array $pFilters - Filters.
     * @param array $pLiked - Fields to which the LIKE operator will be applied.
     */
    public function scopeFilters(Builder $pQuery, array $pFilters = [], array $pLiked = []): void
    {
        foreach ($pFilters as $key => $value) {
            if ($key === 'initial') {
                $signalCompare = '>=';
                $key = 'created_at';
            } elseif ($key === 'final') {
                $signalCompare = '<=';
                $key = 'created_at';
            } elseif (in_array($key, $pLiked)) {
                $signalCompare = 'LIKE';
                $value = "%{$value}%";
            } else {
                $signalCompare = '=';
            }

            $table = (str_contains($key, '.')) ? $key : ((isset($this->table) && ! empty($this->table)) ? "{$this->table}.{$key}" : $key);
            $pQuery->where($table, $signalCompare, $value);
        }
    }

    /**
     * Get all registers
     * 
     * @param array $pColumns - Arrays Columns
     * @return Collection
     */
    public static function getAll(array $pColumns = ['*']): Collection
    {
        return self::all($pColumns);
    }

    /**
     * Get by Id
     * 
     * @param int $pId - id Register
     * @return self
     */
    public static function getById(int $pId)
    {
        return self::find($pId);
    }

    /**
     * Get filtered and paginated data
     * 
     * @param array $pFilters - Filter Array
     * @param string $pOrderBy - Sort field
     * @param string $pDirection - Ordination Direction
     * @return LengthAwarePaginator
     */
    public static function filtered(array $pFilters, string $pOrderBy = null, string $pDirection = 'ASC'): LengthAwarePaginator
    {
        $currentPage = request('page', 1);
        $query = self::query()->filters($pFilters);
        if ($pOrderBy) {
            $query->orderBy($pOrderBy, $pDirection);
        }

        $fetchRows = $query->get();

        $paginated = new LengthAwarePaginator(
            $fetchRows->forPage($currentPage, self::$perpage),
            $fetchRows->count(),
            self::$perpage,
            $currentPage
        );

        return $paginated;
    }

    /**
     * Get the first item by filters
     * 
     * @param array $pFilters - Filter Array
     * @return self|null
     */
    public static function firstByFilters(array $pFilters): self|null
    {
        $fetchRow = self::query()
            ->filters($pFilters)
            ->first();

        return $fetchRow;
    }

    /**
     * Check if id already exists
     * 
     * @param int $pId - The record id
     * @return bool
     */
    public static function isExists(int $pId): bool
    {
        return self::getById($pId) ? true : false;
    }

    /**
     * Get relationships
     * 
     * @param int $pId - The record id
     * @param array $pRelations - the relationships
     * @return Model|null
     */
    public static function showWith(int $pId, array $pRelations = []): ?Model
    {
        return self::with($pRelations)->find($pId);
    }

    /**
     * Update register
     * 
     * @param int $pId - The record id
     * @param array $pData - Data Update
     * @param array $pAdditionalConditions - Additional filters.
     * @return bool|null
     */
    public static function updateWith(int $pId, array $pData, array $pAdditionalConditions = []): ?bool
    {
        $query = self::where('id', $pId);
        foreach ($pAdditionalConditions as $key => $value) {
            $query->where($key, $value);
        }

        return $query->update($pData);
    }
}

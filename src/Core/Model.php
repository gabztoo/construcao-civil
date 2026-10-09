<?php

abstract class Model
{
    public static string $table = '';
    public static string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = ['senha_hash'];
    protected array $casts = [];

    public static function query(): Builder
    {
        return new Builder(static::class);
    }

    public static function find(mixed $id): ?static
    {
        return static::query()->where(static::$primaryKey, $id)->first();
    }

    public static function create(array $data): static
    {
        $instance = new static();
        $instance->fill($data);
        $instance->save();
        return $instance;
    }

    public static function firstOrCreate(array $attributes, array $values = []): static
    {
        $model = static::query()->where($attributes)->first();
        if ($model) return $model;
        return static::create(array_merge($attributes, $values));
    }

    public function fill(array $data): static
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public function save(): bool
    {
        $data = $this->getAttributes();
        
        if (!empty($data[static::$primaryKey])) {
            return $this->update($data);
        }
        
        return $this->insert($data);
    }

    protected function insert(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = ':' . implode(', :', $columns);
        $sql = "INSERT INTO " . static::$table . " (" . implode(', ', $columns) . ") VALUES ({$placeholders})";
        
        $stmt = Database::getInstance()->prepare($sql);
        $result = $stmt->execute($data);
        
        if ($result) {
            $this->{static::$primaryKey} = Database::getInstance()->lastInsertId();
        }
        
        return $result;
    }

    protected function update(array $data): bool
    {
        $id = $data[static::$primaryKey];
        unset($data[static::$primaryKey]);
        
        if (empty($data)) return true;
        
        $sets = implode(', ', array_map(fn($col) => "{$col} = :{$col}", array_keys($data)));
        $sql = "UPDATE " . static::$table . " SET {$sets} WHERE " . static::$primaryKey . " = :id";
        $data['id'] = $id;
        
        $stmt = Database::getInstance()->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(): bool
    {
        $id = $this->{static::$primaryKey};
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id";
        $stmt = Database::getInstance()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getAttributes(): array
    {
        $attrs = [];
        foreach ($this->fillable as $field) {
            if (property_exists($this, $field)) {
                $attrs[$field] = $this->{$field};
            }
        }
        if (property_exists($this, static::$primaryKey)) {
            $attrs[static::$primaryKey] = $this->{static::$primaryKey};
        }
        return $attrs;
    }

    public function toArray(): array
    {
        $data = $this->getAttributes();
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        return $data;
    }

    public function __get(string $name): mixed
    {
        return $this->{$name} ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }
}

class Builder
{
    private string $modelClass;
    private array $wheres = [];
    private array $params = [];
    private string $orderBy = '';
    private int $limit = 0;
    private int $offset = 0;
    private array $select = ['*'];

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function select(array $columns): static
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, mixed $operator = null, mixed $value = null): static
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "{$column} IN ({$placeholders})";
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy = "ORDER BY {$column} " . strtoupper($direction);
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM " . $this->modelClass::$table;
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        if ($this->orderBy) {
            $sql .= " " . $this->orderBy;
        }
        
        if ($this->limit > 0) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset > 0) {
            $sql .= " OFFSET {$this->offset}";
        }

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($this->params);
        $results = $stmt->fetchAll();

        return array_map(fn($row) => $this->hydrate($row), $results);
    }

    public function first(): ?object
    {
        $this->limit = 1;
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM " . $this->modelClass::$table;
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($this->params);
        return (int) ($stmt->fetch()['total'] ?? 0);
    }

    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $total = $this->count();
        $this->limit = $perPage;
        $this->offset = ($page - 1) * $perPage;
        $data = $this->get();

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
            'from' => $total > 0 ? ($page - 1) * $perPage + 1 : 0,
            'to' => min($page * $perPage, $total),
        ];
    }

    private function hydrate(array $row): object
    {
        $model = new $this->modelClass();
        foreach ($row as $key => $value) {
            $model->{$key} = $value;
        }
        return $model;
    }
}
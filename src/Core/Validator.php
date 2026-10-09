<?php

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $validated = [];
    private array $messages = [
        'required' => 'O campo :attribute é obrigatório.',
        'email' => 'O campo :attribute deve ser um e-mail válido.',
        'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
        'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
        'numeric' => 'O campo :attribute deve ser numérico.',
        'integer' => 'O campo :attribute deve ser um número inteiro.',
        'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
        'date' => 'O campo :attribute deve ser uma data válida.',
        'date_format' => 'O campo :attribute deve estar no formato :format.',
        'after' => 'O campo :attribute deve ser posterior a :date.',
        'before' => 'O campo :attribute deve ser anterior a :date.',
        'unique' => 'Este :attribute já está em uso.',
        'exists' => 'O :attribute selecionado não existe.',
        'in' => 'O campo :attribute deve ser um dos valores: :values.',
        'regex' => 'O campo :attribute possui formato inválido.',
        'confirmed' => 'A confirmação do campo :attribute não confere.',
        'cpf_cnpj' => 'O campo :attribute deve ser um CPF ou CNPJ válido.',
        'money' => 'O campo :attribute deve ser um valor monetário válido.',
        'file' => 'O campo :attribute deve ser um arquivo.',
        'image' => 'O campo :attribute deve ser uma imagem.',
        'mimes' => 'O campo :attribute deve ser do tipo: :values.',
        'max_file' => 'O campo :attribute não pode exceder :max KB.',
    ];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function passes(): bool
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $rules = explode('|', $ruleString);

            foreach ($rules as $rule) {
                $params = [];
                if (preg_match('/^(\w+):(.+)$/', $rule, $matches)) {
                    $rule = $matches[1];
                    $params = explode(',', $matches[2]);
                }

                $method = "validate{$rule}";
                if (method_exists($this, $method)) {
                    if (!$this->$method($field, $value, $params)) {
                        break;
                    }
                }
            }
        }

        $this->validated = array_intersect_key($this->data, array_flip(array_keys($this->rules)));
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->validated;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    private function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->messages[$rule] ?? "O campo :attribute falhou na validação :rule.";
        $attribute = ucfirst(str_replace('_', ' ', $field));
        
        $replacements = [
            ':attribute' => $attribute,
            ':min' => $params[0] ?? '',
            ':max' => $params[0] ?? '',
            ':format' => $params[0] ?? '',
            ':date' => $params[0] ?? '',
            ':values' => implode(', ', $params),
            ':rule' => $rule,
        ];

        $message = strtr($message, $replacements);
        $this->errors[$field][] = $message;
    }

    private function getValue(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    private function validateRequired(string $field, mixed $value): bool
    {
        $isEmpty = $value === null || $value === '' || (is_array($value) && empty($value));
        if ($isEmpty) {
            $this->addError($field, 'required');
            return false;
        }
        return true;
    }

    private function validateEmail(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'email');
            return false;
        }
        return true;
    }

    private function validateMin(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        $min = (int) ($params[0] ?? 0);
        if (is_string($value) && mb_strlen($value) < $min) {
            $this->addError($field, 'min', [$min]);
            return false;
        }
        if (is_array($value) && count($value) < $min) {
            $this->addError($field, 'min', [$min]);
            return false;
        }
        return true;
    }

    private function validateMax(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        $max = (int) ($params[0] ?? 0);
        if (is_string($value) && mb_strlen($value) > $max) {
            $this->addError($field, 'max', [$max]);
            return false;
        }
        if (is_array($value) && count($value) > $max) {
            $this->addError($field, 'max', [$max]);
            return false;
        }
        return true;
    }

    private function validateNumeric(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        if (!is_numeric($value)) {
            $this->addError($field, 'numeric');
            return false;
        }
        return true;
    }

    private function validateInteger(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, 'integer');
            return false;
        }
        return true;
    }

    private function validateBoolean(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        if (!in_array($value, [true, false, 'true', 'false', '1', '0', 1, 0], true)) {
            $this->addError($field, 'boolean');
            return false;
        }
        return true;
    }

    private function validateDate(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        $d = DateTime::createFromFormat('Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $this->addError($field, 'date');
            return false;
        }
        return true;
    }

    private function validateDateFormat(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        $format = $params[0] ?? 'Y-m-d';
        $d = DateTime::createFromFormat($format, $value);
        if (!$d || $d->format($format) !== $value) {
            $this->addError($field, 'date_format', [$format]);
            return false;
        }
        return true;
    }

    private function validateAfter(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        $date = $params[0] ?? date('Y-m-d');
        if (strtotime($value) <= strtotime($date)) {
            $this->addError($field, 'after', [$date]);
            return false;
        }
        return true;
    }

    private function validateBefore(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        $date = $params[0] ?? date('Y-m-d');
        if (strtotime($value) >= strtotime($date)) {
            $this->addError($field, 'before', [$date]);
            return false;
        }
        return true;
    }

    private function validateUnique(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        
        [$table, $column, $ignoreId, $ignoreColumn] = array_pad($params, 4, null);
        $column = $column ?? $field;
        $ignoreColumn = $ignoreColumn ?? 'id';

        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $bindings = [$value];

        if ($ignoreId !== null) {
            $sql .= " AND {$ignoreColumn} != ?";
            $bindings[] = $ignoreId;
        }

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($bindings);
        $count = (int) ($stmt->fetch()['count'] ?? 0);

        if ($count > 0) {
            $this->addError($field, 'unique');
            return false;
        }
        return true;
    }

    private function validateExists(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        
        [$table, $column] = array_pad($params, 2, null);
        $column = $column ?? 'id';

        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([$value]);
        $count = (int) ($stmt->fetch()['count'] ?? 0);

        if ($count === 0) {
            $this->addError($field, 'exists');
            return false;
        }
        return true;
    }

    private function validateIn(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        if (!in_array($value, $params, true)) {
            $this->addError($field, 'in', $params);
            return false;
        }
        return true;
    }

    private function validateRegex(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') return true;
        $pattern = $params[0] ?? '';
        if (!preg_match($pattern, $value)) {
            $this->addError($field, 'regex');
            return false;
        }
        return true;
    }

    private function validateConfirmed(string $field, mixed $value, array $params): bool
    {
        $confirmField = $field . '_confirmation';
        if (($this->data[$confirmField] ?? null) !== $value) {
            $this->addError($field, 'confirmed');
            return false;
        }
        return true;
    }

    private function validateCpfCnpj(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        
        $doc = preg_replace('/\D/', '', $value);
        
        if (strlen($doc) === 11) {
            if (!$this->validarCpf($doc)) {
                $this->addError($field, 'cpf_cnpj');
                return false;
            }
        } elseif (strlen($doc) === 14) {
            if (!$this->validarCnpj($doc)) {
                $this->addError($field, 'cpf_cnpj');
                return false;
            }
        } else {
            $this->addError($field, 'cpf_cnpj');
            return false;
        }
        return true;
    }

    private function validateMoney(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        
        $clean = preg_replace('/[^\d,.]/', '', $value);
        $clean = str_replace(',', '.', $clean);
        
        if (!is_numeric($clean)) {
            $this->addError($field, 'money');
            return false;
        }
        return true;
    }

    private function validateFile(string $field, mixed $value): bool
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, 'file');
            return false;
        }
        return true;
    }

    private function validateImage(string $field, mixed $value): bool
    {
        if (!$this->validateFile($field, $value)) return false;
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $this->addError($field, 'mimes', $allowed);
            return false;
        }
        return true;
    }

    private function validateMimes(string $field, mixed $value, array $params): bool
    {
        if (!$this->validateFile($field, $value)) return false;
        
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $params)) {
            $this->addError($field, 'mimes', $params);
            return false;
        }
        return true;
    }

    private function validateMaxFile(string $field, mixed $value, array $params): bool
    {
        if (!$this->validateFile($field, $value)) return false;
        
        $maxKb = (int) ($params[0] ?? 2048);
        $sizeKb = $_FILES[$field]['size'] / 1024;
        
        if ($sizeKb > $maxKb) {
            $this->addError($field, 'max_file', [$maxKb]);
            return false;
        }
        return true;
    }

    private function validarCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
        
        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int)$cpf[$i] * ($t + 1 - $i);
            }
            $digit = ($sum * 10) % 11;
            $digit = $digit === 10 ? 0 : $digit;
            if ((int)$cpf[$t] !== $digit) return false;
        }
        return true;
    }

    private function validarCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) return false;
        
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$cnpj[$i] * $weights1[$i];
        }
        $digit1 = $sum % 11;
        $digit1 = $digit1 < 2 ? 0 : 11 - $digit1;
        
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int)$cnpj[$i] * $weights2[$i];
        }
        $digit2 = $sum % 11;
        $digit2 = $digit2 < 2 ? 0 : 11 - $digit2;
        
        return (int)$cnpj[12] === $digit1 && (int)$cnpj[13] === $digit2;
    }
}
<?php

declare(strict_types=1);
namespace Widmogrod\Monad\Control\Doo\Registry;

class Registry
{
    private $data = [];

    /**
     * @param  string                   $name
     * @return mixed
     * @throws VariableNotDeclaredError
     */
    public function get(string $name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new VariableNotDeclaredError($name);
    }

    /**
     * @param  string                       $name
     * @param  mixed                        $value
     * @return mixed
     * @throws CannotRedeclareVariableError
     */
    public function set(string $name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            throw new CannotRedeclareVariableError($name, array_keys($this->data));
        }

        return $this->data[$name] = $value;
    }
}

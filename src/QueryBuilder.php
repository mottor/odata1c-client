<?php
declare(strict_types=1);

namespace Dakword\OData1C\Client;

class QueryBuilder
{
    private Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function build(string $guid = ''): string
    {
        $query = $this->query->entitySet;

        if (!empty($guid)) {
            $query .= "(guid'{$guid}')";
        } elseif ($this->query->compositeKey) {
            $query .= $this->compositeKey();
        } elseif ($this->query->functions) {
            $query .= $this->function();
        }

        $params = [];

        $select = $this->select();
        if (!empty($select)) {
            $params[] = $select;
        }

        if (empty($guid)) {
            $filter = $this->filter();
            if (!empty($filter)) {
                $params[] = $filter;
            }
            $order = $this->orderBy();
            if (!empty($order)) {
                $params[] = $order;
            }
            $expand = $this->expand();
            if (!empty($expand)) {
                $params[] = $expand;
            }

            if (!empty($this->query->skip)) {
                $params[] = '$skip=' . $this->query->skip;
            }
            if (!empty($this->query->top)) {
                $params[] = '$top=' . $this->query->top;
            }
            if ($this->query->inlineCount) {
                $params[] = '$inlinecount=allpages';
            }
            if (!is_null($this->query->allowedOnly)) {
                $params[] = '$allowedOnly=' . ($this->query->allowedOnly ? 'true' : 'false');
            }
        }

        if ($params) {
            $query .= '?' . implode('&', $params);
        }

        return $query;
    }

    public function function(): string
    {
        if (empty($this->query->functions)) {
            return '';
        }
            $name = $this->query->functions[0];
            $params = $this->query->functions[1];
            $query = "/{$name}(";
            $query .= implode(',', array_map(function ($name, $value) {
                return "{$name}={$value}";
            }, array_keys($params), $params));
            $query .= ")";
        return $query;
    }

    public function compositeKey(): string
    {
        // (Объект='f3a87401-1159-11eb-814a-00155d02f40f', Объект_Type='StandardODATA.Catalog_Контрагенты', Свойство_Key=guid'f3a873f4-1159-11eb-814a-00155d02f40f')
        if (empty($this->query->compositeKey)) {
            return '';
        }
        $query = "(";
        $query .= implode(', ', array_map(function ($name, $value) {
            return "{$name}={$value}";
        }, array_keys($this->query->compositeKey), $this->query->compositeKey));
        $query .= ")";
        return $query;
    }

    public function select(): string
    {
        if (empty($this->query->selects)) {
            return '';
        }
        return '$select=' . implode(',', $this->query->selects);
    }

    public function expand(): string
    {
        if (empty($this->query->expands)) {
            return '';
        }
        return '$expand=' . implode(',', $this->query->expands);
    }

    public function filter(): string
    {
        if (empty($this->query->wheres)) {
            return '';
        }
        $first = true;
        return '$filter=' . implode('', array_map(function ($where) use (&$first) {
                    if (!is_array($where)) {
                        return $where;
                    }
                    if ($first) {
                        if (!isset($where[2])) {
                            $first = false;
                        }
                        return $where[1];
                    } else {
                        if (isset($where[2])) {
                            $first = true;
                        }
                        return ' ' . $where[0] . ' ' . $where[1];
                    }
                }, $this->query->wheres)
            );
    }

    public function orderBy(): string
    {
        if (empty($this->query->orders)) {
            return '';
        }
        return '$orderby=' . implode(',', array_map(function ($order) {
                return trim($order[0]) . ' ' . trim($order[1]);
            }, $this->query->orders));
    }

}

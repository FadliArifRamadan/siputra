<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait DataTableFilterTrait
{
    /**
     * Apply common filters to DataTable query.
     */
    protected function applyFilters($query, Request $request, array $filterConfig)
    {
        foreach ($filterConfig as $field => $config) {
            $value = $request->input($field);
            
            if ($value === null || $value === '' || $value === 'all') {
                continue;
            }

            $operator = $config['operator'] ?? '=';
            $column = $config['column'] ?? $field;

            switch ($operator) {
                case 'like':
                    $query->where($column, 'like', '%' . $value . '%');
                    break;
                case 'between':
                    if (isset($config['start']) && isset($config['end'])) {
                        $start = $request->input($config['start']);
                        $end = $request->input($config['end']);
                        if ($start && $end) {
                            $query->whereBetween($column, [$start, $end]);
                        }
                    }
                    break;
                case 'in':
                    if (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                    break;
                default:
                    $query->where($column, $operator, $value);
                    break;
            }
        }

        return $query;
    }

    /**
     * Get filter HTML for view.
     */
    protected function getFilterHtml(array $filters): string
    {
        $html = '<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">';
        
        foreach ($filters as $key => $config) {
            $label = $config['label'] ?? ucfirst(str_replace('_', ' ', $key));
            $type = $config['type'] ?? 'text';
            $options = $config['options'] ?? [];
            $placeholder = $config['placeholder'] ?? 'Semua';

            $html .= '<div>';
            $html .= '<label class="block text-xs font-medium text-gray-700 mb-1">' . $label . '</label>';

            if ($type === 'select') {
                $html .= '<select name="' . $key . '" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">';
                $html .= '<option value="">' . $placeholder . '</option>';
                foreach ($options as $value => $text) {
                    $selected = request($key) == $value ? 'selected' : '';
                    $html .= '<option value="' . $value . '" ' . $selected . '>' . $text . '</option>';
                }
                $html .= '</select>';
            } elseif ($type === 'date') {
                $html .= '<input type="date" name="' . $key . '" value="' . request($key) . '" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">';
            } elseif ($type === 'date_range') {
                $html .= '<div class="flex gap-1">';
                $html .= '<input type="date" name="' . $key . '_start" value="' . request($key . '_start') . '" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Dari">';
                $html .= '<input type="date" name="' . $key . '_end" value="' . request($key . '_end') . '" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Sampai">';
                $html .= '</div>';
            } else {
                $html .= '<input type="text" name="' . $key . '" value="' . request($key) . '" placeholder="' . $placeholder . '" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">';
            }

            $html .= '</div>';
        }

        $html .= '<div class="flex items-end gap-2">';
        $html .= '<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition text-sm">Filter</button>';
        $html .= '<a href="' . request()->url() . '" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition text-sm">Reset</a>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
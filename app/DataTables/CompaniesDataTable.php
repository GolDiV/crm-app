<?php

namespace App\DataTables;

use App\Models\CompanyIndex;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CompaniesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<CompanyIndex>  $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            // чекбокс в строке
            ->addColumn(
                'checkbox',
                fn(CompanyIndex $row) =>
                '<div class="text-center align-middle">
                    <input type="checkbox" class="row-checkbox" name="selected[]" value="' . $row->id . '">
                 </div>'
            )

            // телефоны — форматируем для UI (поиск пойдёт по phones_digits)
            ->editColumn('phones_csv', function (CompanyIndex $row) {
                $items = collect(explode(', ', (string) $row->phones_csv))
                    ->filter()
                    ->map(function ($raw) {
                        $digits = preg_replace('/\D/', '', $raw);
                        if (preg_match('/^7(\d{3})(\d{3})(\d{2})(\d{2})$/', $digits, $m)) {
                            // 8&nbsp;(XXX)&nbsp;XXX&#8209;XX&#8209;XX
                            return '8&nbsp;(' . $m[1] . ')&nbsp;' . $m[2] . '&#8209;' . $m[3] . '&#8209;' . $m[4];
                        }
                        return e($raw);
                    });

                return $items->implode(', ');
            })

            // глобальный поиск по агрегатам/цифрам
            ->filterColumn('sectors_csv', function ($q, $keyword) {
                $q->where('sectors_csv', 'like', '%' . mb_strtolower($keyword) . '%');
            })
            ->filterColumn('emails_lc', function ($q, $keyword) {
                $q->where('emails_lc', 'like', '%' . mb_strtolower($keyword) . '%');
            })
            ->filterColumn('phones_csv', function ($q, $keyword) {
                $digits = preg_replace('/\D/', '', (string) $keyword);
                if ($digits !== '') {
                    $q->where('phones_digits', 'like', '%' . $digits . '%');
                } else {
                    $q->where('phones_csv', 'like', '%' . $keyword . '%');
                }
            })

            ->rawColumns(['checkbox', 'phones_csv'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<CompanyIndex>
     */
    public function query(CompanyIndex $model): QueryBuilder
    {
        // Никаких join/with — всё уже денормализовано в company_index
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        // локаль как у тебя
        $lang = json_decode(file_get_contents(
            resource_path('js/datatables/ru.json')
        ), true);

        return $this->builder()
            ->setTableId('companies-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide()
            ->processing()
            ->orderBy(1, 'asc') // 0: checkbox, 1: id(hidden), 2: short_name
            ->addTableClass('table-sm small table-striped')
            ->parameters([
                'deferRender' => true,
                'searchDelay' => 400,
                'order' => [[1, 'asc']],
                'columns' => [
                    ['orderable' => false, 'searchable' => false], // 0 checkbox
                    ['visible' => false, 'searchable' => false],   // 1 id
                    null, // 2 short_name
                    null, // 3 region_name
                    null, // 4 city
                    null, // 5 sectors_csv
                    null, // 6 phones_csv
                    null, // 7 emails_lc
                    null, // 8 manager_name
                ],
                'language' => $lang,
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')
                ->title('<div style="margin-left: 5px;"><input type="checkbox" data-select-all id="select-all"></div>')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(30)
                ->addClass('text-center'),

            Column::make('id')
                ->title('Id')
                ->visible(false)
                ->searchable(false),

            Column::make('short_name')->title('Название'),
            Column::make('region_name')->title('Регион'),
            Column::make('city')->title('Город'),

            // ниже — агрегаты/денормализованные поля из company_index
            Column::make('sectors_csv')->title('Виды деятельности'),
            Column::make('phones_csv')->title('Телефоны')->escape(false),
            Column::make('emails_lc')->title('Email'),

            // фамилия менеджера (последнее слово из users.name в индексе)
            Column::make('manager_name')->title('Менеджер'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Companies_' . date('YmdHis');
    }
}

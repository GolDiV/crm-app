<?php

namespace App\DataTables;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class CompaniesDataTable extends DataTable
{

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Company> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn(Company $company) =>
            '<div class="text-center align-middle">
        <input type="checkbox" class="row-checkbox" name="selected[]" value="' . $company->id . '">
    </div>')
            ->addColumn('sectors', function (Company $company) {
                $sectors = $company->sectors->pluck('name')->implode(', ');
                return mb_strtolower($sectors);
            })
            ->addColumn('phones', function (Company $company) {
                return $company->phones
                    ->map(function ($phone) {
                        $digits = preg_replace('/\D/', '', $phone->number);
                        if (preg_match('/^7(\d{3})(\d{3})(\d{2})(\d{2})$/', $digits, $m)) {
                            return '8&nbsp;(' . $m[1] . ')&nbsp;' . $m[2] . '&#8209;' . $m[3] . '&#8209;' . $m[4];
                        }
                        return $phone->number;
                    })
                    ->implode(', ');
            })
            ->addColumn('emails', function (Company $company) {
                return $company->emails
                    ->pluck('email')
                    ->implode(', ');
            })
            ->addColumn('user', function (Company $company) {
                $userName = $company->user ? $company->user->name : '';
                $lastWord = Str::of(trim($userName))
                    ->whenEmpty(fn() => null) // если пустая строка — сразу null
                    ->explode(' ')
                    ->filter()                 // убираем пустые элементы
                    ->last();
                return $lastWord ?? '';
            })
            ->filterColumn('region_name', function ($query, $keyword) {
                $query->where('regions.name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['checkbox', 'phones', 'emails'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Company>
     */
    public function query(Company $model): QueryBuilder
    {
        return $model->newQuery()
            ->leftJoin('regions', 'regions.id', '=', 'companies.region_id')
            ->select('companies.*', 'regions.name as region_name') // <-- ключ
            ->with(['sectors', 'phones', 'emails']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $lang = json_decode(file_get_contents(
            resource_path('js/datatables/ru.json')
        ), true);

        return $this->builder()
            ->setTableId('companies-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'asc')
            //->selectStyleSingle()
            ->addTableClass('table-sm small table-striped')
            /*  ->buttons([
                Button::make('add'),
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]) */
            ->parameters([
                'order' => [[1, 'asc']], // сортировать по short_name (3-й столбец визуально)
                'columns' => [
                    ['orderable' => false], // checkbox (0)
                    ['visible' => false, 'searchable' => false], // id (1)
                    null, // short_name (2)
                    null, // region
                    null, // city
                    null, // sectors
                    null, // phones
                    null, // emails
                    null, // user
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
                ->visible(false)       // скрыть в таблице
                ->searchable(false),
            Column::make('short_name')->title('Название'),
            Column::make('region_name')->title('Регион'),
            Column::make('city')->title('Город'),
            Column::make('sectors')->title('Виды деятельности'),
            Column::make('phones')->title('Телефоны'),
            Column::make('emails')->title('Email'),
            Column::make('user')->title('Менеджер'),
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

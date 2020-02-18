<?php

namespace Fenestron\NovaGaPageViews\Http\Controllers;

use Illuminate\Routing\Controller;
use Fenestron\NovaGaPageViews\Enums\ListCategory;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class GetPageViews
 * @package Fenestron\NovaGaPageViews\Http\Controllers
 */
class GetPageViews extends Controller
{
    public function __invoke()
    {
        if (! request()->has('category')) {
            throw new HttpException(400,'Category must be filled.');
        }

        $metrics = [
            'metrics' => 'ga:pageviews',
            'dimensions' => 'ga:pageTitle,ga:hostname,ga:pagePath',
            'sort' => '-ga:pageviews',
        ];

        switch (request()->get('category')) {
            case ListCategory::ALL:
                $title = 'Просмотры по страницам';
                break;
            case ListCategory::TOP:
                $title = 'Топ посещаемых страниц';
                $metrics = array_merge($metrics, [
                    'max-results' => 10,
                ]);
                break;
            case ListCategory::ACTIVITIES:
                $title = 'Просмотры по услугам';
                $metrics = array_merge($metrics, [
                    'filters' => 'ga:pagePath=@/a/',
                ]);
                break;
            default:
                throw new HttpException(422, 'Category dont exist.');
        }

        $analyticsData = app(Analytics::class)->performQuery(
            Period::days(7),
            'ga:users',
            $metrics
        );

        $headers = ['name', 'hostname', 'path', 'visits'];

        $pages = array_map(
            function ($row) use ($headers) {
                return array_combine($headers, $row);
            },
            $analyticsData->rows ?? []
        );

        return compact('title', 'pages');
    }
}

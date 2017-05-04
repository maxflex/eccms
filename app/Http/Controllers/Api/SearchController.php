<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use Illuminate\Support\Facades\DB;
use Validator;

class SearchController extends Controller
{
    /**
     * @param Request $request
     */
    public function search(Request $request)
    {
        # правила валидации
        $rules = [
            'query' => 'required'
        ];

        # текст для ошибок обработки валидации
        $messages = [
            'required' => 'Запрос не должен быть пустым',
        ];

        # проверка
        $validator = Validator::make($request->all(), $rules, $messages);

        if (! $validator->fails()) {
            $query = trim($request->input('query'));

            # очистка и разбиение запроса на ключевые слова и формирование текста для FULLTEXT
            $queryArray = explode(' ', $query);


            # поля переменных
            $variable_fields = ['html'];

            # поиск по ученикам
            $variables_query = DB::table('variables')->select('id', 'name');

            foreach ($queryArray as $word) {
                $variables_query->where(function ($query) use ($word, $variable_fields) {
                    foreach ($variable_fields as $field) {
                        $query->orWhere($field, 'LIKE', '%' . $word . '%');
                    }
                    return $query;
                });
            }

            $variables = $variables_query
                ->orderBy('id', 'desc')
                ->take(30)
                ->get();

            # поля по которым должен искать преподовательский поиск
            $page_fields = [
                'html',
                'html_mobile',
                'seo_text',
            ];

            # поиск по по предователям
            $pages_query = DB::table('pages')->select('id', 'keyphrase');
            foreach ($queryArray as $word) {
                $pages_query->where(function ($query) use ($word, $page_fields) {
                    foreach ($page_fields as $field) {
                        $query->orWhere($field, 'LIKE', '%' . $word . '%');
                    }
                    return $query;
                });
            }

            $pages = $pages_query->take(30)
                ->orderBy('id')
                ->take(30)
                ->get();

            $results = count($variables) + count($pages);
            return compact('variables', 'pages', 'results');
        } else {
            return response($validator->errors()->all(), 400);
        }
    }
}
<?php

namespace App\Http\Controllers\Analysis;

use Illuminate\Http\Request;

class ExportController extends AnalysisController
{
    protected $model = '\App\Models\Manager\Analysis\ExportFileLog';

    protected $checkPermission = true;

    protected $allowExportKeys = [
        'data_report_export' => ['uri' => 'apidata/datareport'],
        'sub_data_report_export' => ['uri' => 'apidata/subdatareport'],
    ];

    protected function getBaseuri()
    {
        return 'http://' . env('DOMAIN_' . strtoupper($this->proj)) . '/';
    }

    public function exportView(Request $request)
    {
        return view('Analysis/Export/exportView', ['pageTitle' => $this->role->getCurrentPageTitle($request)]);
    }

    public function exportList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $key = $request->query->get('key');
        $model = new ($this->model);
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $key && $model = $model->where('key', $key);
        $model->where('admin_id', $this->admin->getLoginID($request));
        $model = $model->select(
            'id',
            'cost_time',
            'created',
            'admin_id',
            'admin_username',
            'errors',
            'key',
            'size',
            'ext',
            'filename',
            'is_success',
        );
        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function exportAdd(Request $request)
    {
        $data = $request->only(
            'key',
            'filename',
        );

        $query = (array) $request->get('query');
        $query['_admin_id'] = $data['admin_id'] = $this->admin->getLoginID($request);
        $query['_admin_username'] = $data['admin_username'] = $this->admin->getLoginUsername($request);

        $config = $this->allowExportKeys[$data['key']] ?? '';
        if (empty($config)) {
            return ['success' => 0, 'result' => __('ts.not allow export task')];
        }

        if ($this->checkPermission && !$this->role->isPermission($request, $data['key'])) {
            return ['success' => 0, 'result' => __('ts.Permission deneid.') . "[" . $data['key'] . ']'];
        }

        $model = new ($this->model);
        $last = $model->where('admin_id', $data['admin_id'])
            ->where('key', $data['key'])
            ->orderBy('id', 'desc')
            ->first();
        if (false && !empty($last) && time() - strtotime($last->created) < 600) {
            return ['success' => 0, 'result' => __('ts.The same form can be submitted once in ten minutes')];
        }

        $today = date('Y-m-d');
        $count = $model->where('admin_id', $data['admin_id'])
            ->where('key', $data['key'])
            ->where('created', '>', $today . ' 00:00:00')
            ->where('created', '>', $today . ' 23:59:59')
            ->count();
        if (false && !empty($last) && $count > 5) {
            return ['success' => 0, 'result' => __('ts.The same table can only be exported 15 times a day')];
        }

        $model = new ($this->model);
        $after = $model->create($data);
        $this->actionLog->create([
            'admin_id' => $this->admin->getLoginID($request),
            'admin_username' => $this->admin->getLoginUsername($request),
            'browser' => $request->header('User-Agent'),
            'key' => 'EXPORT_CREATE',
            'is_success' => 1,
            'url' => $request->url(),
            'ip' => $this->ip($request),
            'desc' => $after->username,
            'target_id' => $after->id,
            'after' =>  $after->toJson(),
            'params' => json_encode($request->only(
                'key',
                'filename',
                'query',
                'columns',
            )),
            'method' => $request->method()
        ]);

        $queue = 'export';
        $res = \App\Jobs\ExportFile::dispatch([
            'model' => $after,
            'query' => $query,
            'columns' => $request->input('columns'),
            'baseuri' => $this->getBaseuri(),
            'uri' => $config['uri'],
            'requireItems' => $request->input('requireItems'),
            'baseDataPath' => $this->baseDataPath,
            'proj' => $this->proj,
        ])->onQueue($queue);

        // $res = \App\Jobs\ExportFile::dispatch([
        //     'id' => $after,
        //     'query' => $request->input('query'),
        //     'columns' => $request->input('columns')
        // ]);
        return ['success' => 1, 'result' => __('ts.Table export task has started'), 'res' => $res];
    }

    public function exportDownload(Request $request, $id)
    {
        $model = new ($this->model);
        $res = $model->where('id', $id)->where('admin_id', $this->admin->getLoginID($request))->first();
        if ($res) {
            return response()
                ->download(
                    storage_path('export_file' . '/' . $this->proj . '/' . date('Ymd', strtotime($res->created)) . '/' . $res->key . '_' . $res->id . '.csv')
                );
        }
    }
}

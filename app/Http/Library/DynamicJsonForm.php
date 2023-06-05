<?php

namespace App\Http\Library;

class DynamicJsonForm
{

    protected $data;

    protected $config;

    protected $postField = 'json';

    protected $add;

    protected $del;

    protected $valid = true;

    protected $type = 0;

    public function __construct($config)
    {
        $this->config = $config;
        return $this;
    }

    public function fill($data, $isJson = true)
    {
        $data = $isJson ? json_decode($data, true) : $data;
        $this->valid = true;
        $this->config = $this->_fill([], $this->config, $data);
        return $this;
    }

    public function setPostField($name)
    {
        $this->postField = $name;
        return $this;
    }

    public function addField($name)
    {
        $this->add = $name;
        return $this;
    }

    public function delField($name)
    {
        $this->del = $name;
        return $this;
    }

    protected function _fill($parent, $config, $data)
    {
        $new = [];
        foreach ($config as $c) {

            if (!empty($parent)) {
                $temp = array_merge($parent, [$c]);
            } else {
                $temp = [$c];
            }
            if (isset($c['valType']) && ($c['valType'] == 'arrayMuti')) {
                $struct = $c['val'][0];
                $len = isset($data[$c['field']]) ? count($data[$c['field']]) : 0;
                if ($c['field'] == $this->add) {
                    $len++;
                }

                if ($c['field'] == $this->del) {
                    $len--;
                }

                $temp = [];
                $c['val'] = [];
                for ($i = 0; $i < $len; $i++) {
                    $c['val'][$i] = [];
                    foreach ($struct as $k => $v) {
                        $v['val'] = isset($data[$c['field']][$i][$v['field']]) ? $data[$c['field']][$i][$v['field']] : null;
                        $this->checkValue($v);
                        $struct[$k] = $v;
                    }

                    $c['val'][$i] = $struct;
                }
            } else if (isset($c['valType']) && ($c['valType'] == 'array')) {
                foreach ($c['val'] as $k => $v) {
                    $v['val'] = isset($data[$c['field']]) && isset($data[$c['field']][$v['field']]) ? $data[$c['field']][$v['field']] : null;

                    if (isset($v['valType']) && $v['valType'] == 'arrayVal') {
                        if ($this->add && $this->isCurrentField($temp, $v, $this->add)) {

                            $v['val'][] = '';
                        }

                        if ($this->del && $this->isCurrentField($temp, $v, $this->del)) {
                            array_pop($v['val']);
                        }
                        $v['valNum'] = is_array($v['val']) ? count($v['val']) : 0;
                    }

                    $this->checkValue($v);
                    $c['val'][$k] = $v;
                }
            } else if (isset($c['valType']) && $c['valType'] == 'arrayVal') {
                if ($this->add && $this->isCurrentField($temp, $c, $this->add)) {
                    $data[$c['field']][] = '';
                }

                if ($this->del && $this->isCurrentField($temp, $c, $this->del)) {
                    array_pop($data[$c['field']]);
                }

                $c['val'] = $data[$c['field']];
                $c['valNum'] = count($c['val']);

                $this->checkValue($c);
            } else {
                $c['val'] = isset($data[$c['field']]) ? $data[$c['field']] : 0;
                $this->checkValue($c);
            }
            $new[] = $c;
        }
        return $new;
    }

    protected function isCurrentField($parent, $config, $f)
    {
        if (!empty($parent)) {
            foreach ($parent as $v) {
                $field[] = $v['field'];
            }
        }

        if ($config !== null && isset($config['field']) && end($field) != $config['field']) {
            $field[] = $config['field'];
        }

        $field = implode('_', $field);
        return $field == $f;
    }

    protected function checkValueByMonitorFormat($config)
    {
        $valid = true;
        $message = '';
        $vals = explode('/', $config['val']);
        if (count($vals) != 5) {
            $valid = false;
            $message = $config['field'] . "value format error";
        }

        if ($valid && !in_array($vals[0], [0, 1])) {
            $valid = false;
            $message = $config['field'] . "[0] must be 0 or 1";
        }

        if ($valid) {
            foreach ($vals as $k => $val) {
                if (!is_numeric($val)) {
                    $valid = false;
                    $message = $config['field'] . "[{$k}] must be number";
                    break;
                }
            }
        }
        return [$valid, $message];
    }

    protected function checkValueByNumbersFormat($config)
    {
        $valid = true;
        $message = '';
        $vals = explode(',', $config['val']);
        foreach ($vals as $k => $val) {
            if (!is_numeric($val)) {
                $valid = false;
                $message = $config['field'] . "[{$k}] must be number";
                break;
            }
        }
    
        return [$valid, $message];
    }

    protected function checkValue(&$config)
    {
        if ($config['valType'] == 'array' || $config['valType'] == 'arrayMuti') {
            return;
        }

        if ($config['valType'] == 'integer' && !is_numeric($config['val'])) {
            $config['error'] = $config['field'] . " must be integer";
            $this->valid = false;
        }

        if (isset($config['valCheckType'])) {
            $func = 'checkValueBy' . $config['valCheckType'];
            $res = $this->$func($config);
            if ($res[0] == false) {
                $config['error'] = $res[1];
                $this->valid = false;
            }
        }

        if ($config['valType'] == 'textareaArray') {
            $values = explode("\n", $config['val']);
            $columns = $config['columns'][0];
            $format = implode(',', array_column($columns, 'field'));
            foreach ($values ?? [] as $k => $v) {
                $vs = explode(",", $v);
                if (count($vs) != count($columns)) {
                    $ks = $k + 1;
                    $config['error'] = $config['field'] . "[{$ks}] format is error [{$format}]";
                    $this->valid = false;
                    break;
                }

                foreach ($columns as $ck => $cv) {
                    if ($cv['valType'] == 'integer' && !is_numeric($vs[$ck])) {
                        $ks = $k + 1;
                        $cks = $ck + 1;
                        $config['error'] = $config['field'] . "[{$ks}][{$cks}] must be integer [{$format}]";
                        $this->valid = false;
                        break;
                    }
                }

                if (!$this->valid) {
                    break;
                }
            }
        }


        // print_r($config);
        if ($config['valType'] == 'arrayVal') {
            $vals = (array) $config['val'];
            if ($config['valSubType'] == 'integer') {
                foreach ((array) $config['val'] as $k => $v) {
                    $i = $k + 1;
                    if (!is_numeric($v)) {
                        $config['error'] = $config['field'] . "No.{$i} must be integer";
                        $this->valid = false;
                        break;
                    }

                    if ($this->valid && isset($config['moreThan']) && $v <= $config['moreThan']) {
                        $config['error'] = $config['field'] . "No.{$i} must be more than {$config['moreThan']}";
                        $this->valid = false;
                        break;
                    }

                    if ($this->valid && isset($config['lessThan']) && $v <= $config['lessThan']) {
                        $config['error'] = $config['field'] . "No.{$i} must be less than {$config['lessThan']}";
                        $this->valid = false;
                        break;
                    }
                }
            }

            if ($this->valid && isset($config['require']) && count($vals) != $config['require']) {
                $config['error'] = $config['field'] . "Array length must be equal {$config['require']}";
                $this->valid = false;
            }
        }
    }

    public function isValid()
    {
        return $this->valid;
    }

    protected function _create0($config, $parent = [])
    {
        $forms = [];
        foreach ($config as $c) {

            if (!empty($parent)) {
                $temp = array_merge($parent, [$c]);
            } else {
                $temp = [$c];
            }

            if (isset($c['valType']) && ($c['valType'] == 'arrayMuti')) {

                $c['act'] = isset($c['act']) ? $c['act'] : true;
                $button = $c['act'] ? $this->addActionButton($temp, $c) : '';
                $forms[] = $this->getTitle($c['name']) . '<div class="col-md-10">' . $this->getError($c) . $button . '</div>';
                foreach ($c['val'] as $kk => $vs) {
                    foreach ($vs as $k => $v) {
                        $forms[] = $this->getLabel($v['name'] . "[{$kk}]") . '<div class="col-md-10">' . $this->fetchArrayTextField($temp, $v, $kk) . $this->getError($v) . '</div>';
                    }
                }
            } else if (isset($c['valType']) && ($c['valType'] == 'arrayVal')) {
                $t = [];
                $c['valNum'] = !empty($c['val']) ? count($c['val']) : $c['valNum'];
                for ($i = 0; $i < $c['valNum']; $i++) {
                    $tc = [
                        'val' => is_array($c['val']) && isset($c['val'][$i]) ? $c['val'][$i] : null,
                        'name' => 'No.' . ($i + 1),
                        'valType' => $c['valType2'] ?? '',
                        'options' => $c['options'] ?? [],
                    ];
                    $t[] = $this->fetchArrayTextField($temp, $tc, $i) . (isset($tc['re']) ? " <small class=\"text-muted\" style=\"word-break: break-all; white-space: pre-wrap; \">({$tc['re']})</small>" : '');
                }
                $forms[] = $this->getLabel($c['name']) . '<div class="col-md-10">' . implode(' ', $t) . $this->getError($c) . $this->addActionButton($temp, $c) . '</div>';
            } else if (isset($c['valType']) && ($c['valType'] == 'array')) {
                // array_pop($temp);
                $forms[] = $this->getTitle($c['name']) . '<div class="col-md-10">' . $this->getError($c) . '</div>';
                $forms = array_merge($forms, $this->_create0($c['val'], $temp));
            } else {
                $forms[] = $this->getLabel($c['name']) . '<div class="col-md-10">' . $this->fetchArrayTextField($temp, $c) . $this->getError($c) . '</div>';
            }
        }
        return $forms;
    }

    protected function _create1($config, $parent = [])
    {
        $forms = [];
        $end = false;
        foreach ($config as $c) {

            if (!empty($parent)) {
                $temp = array_merge($parent, [$c]);
            } else {
                $temp = [$c];
            }

            if (isset($c['layout'])) {
                if ($c['layout'] >= 0) {
                    $forms[] = '</div>';
                }
                $forms[] = '<div class="col-md-' . $c['md'] . '">';
                $end = true;
            }

            if (isset($c['valType']) && ($c['valType'] == 'arrayMuti')) {

                $c['act'] = isset($c['act']) ? $c['act'] : true;
                $button = $c['act'] ? $this->addActionButton($temp, $c) : '';
                $forms[] = '<div class="rows gl-3">';
                $forms[] = $this->getTitle($c['name']) . '<div class="col-md-10">' . $this->getError($c) . $button . '</div>';
                foreach ($c['val'] as $kk => $vs) {
                    foreach ($vs as $k => $v) {
                        $forms[] = $this->getLabel($v['name'] . "[{$kk}]") . '<div class="col-md-10">' . $this->fetchArrayTextField($temp, $v, $kk) . $this->getError($v) . '</div>';

                        // $forms[] = '<div class="col-md-6">'.$this->getLabel2($v['name'] . "[{$kk}]") . '<div class="">' . $this->fetchArrayTextField($temp, $v, $kk) . $this->getError($v) . '</div></div>';
                    }
                }
                $forms[] = '</div>';
            } else if (isset($c['valType']) && ($c['valType'] == 'arrayVal')) {
                $t = [];
                $c['valNum'] = !empty($c['val']) ? count($c['val']) : $c['valNum'];
                for ($i = 0; $i < $c['valNum']; $i++) {
                    $tc = [
                        'val' => is_array($c['val']) && isset($c['val'][$i]) ? $c['val'][$i] : null,
                        'name' => 'No.' . ($i + 1),
                        'valType' => $c['valType2'] ?? '',
                        'options' => $c['options'] ?? [],
                    ];
                    $t[] = $this->fetchArrayTextField($temp, $tc, $i) . (isset($tc['re']) ? " <small class=\"text-muted\" style=\"word-break: break-all; white-space: pre-wrap; \">({$tc['re']})</small>" : '');
                }
                $forms[] = $this->getLabel($c['name']) . '<div class="col-md-10">' . implode(' ', $t) . $this->getError($c) . $this->addActionButton($temp, $c) . '</div>';
            } else if (isset($c['valType']) && ($c['valType'] == 'array')) {
                // array_pop($temp);
                $forms[] = $this->getTitle($c['name']) . '<div class="col-md-10">' . $this->getError($c) . '</div>' . '';
                $forms[] = '<div class="rows row-cols-12">';
                $forms = array_merge($forms, $this->_create1($c['val'], $temp));
                $forms[] = '</div>';
            } else {
                $t = $this->getLabel($c['name']) . '<div class="col-md-10">' . $this->fetchArrayTextField($temp, $c) . $this->getError($c) . '</div>';
                if (!empty($parent) && isset($c['md'])) {
                    $forms[] = '<div class="array-control-row col-md-' . $c['md'] . '" style="float:left">' .$t. '</div>';
                } else {
                    $forms[] = $t;
                }
            }
        }

        $end && $forms[] = '</div>';

        if ((!empty($this->_export1) || !empty($this->_export2)) && empty($parent)) {
            $forms = array_merge($forms, $this->_export($config, $parent));
        }
        return $forms;
    }

    protected $_export1;

    protected $_export2;

    public function setExport($v1, $v2)
    {
        $this->_export1 = $v1;
        $this->_export2 = $v2;
        return $this;
    }

    protected function _export($config, $parent = [])
    {
        $forms = [];
        $forms[] = '<div class="col-md-12 form-group" style="margin-top:20px"><h5>Import</h5><input type="file" id="file1" accept=".json" class="form-control dyn-files" placeholder="upload json config"/></div>';
        $forms[] = '<hr>';
        $forms[] = '<div class="col-md-12" style="margin-top:20px">' . PHP_EOL;
        $forms[] = $this->_export2 ? '<a target="_blank" href="' . $this->_export2 . '" class="btn btn-warning">Download Template json file</a>'  . PHP_EOL : '';
        $forms[] = $this->_export1 ? '<a target="_blank" href="' . $this->_export1 . '" class="btn btn-info">Download Current json file</a>' . PHP_EOL : '';
        $forms[] = '</div>' . PHP_EOL;
        return $forms;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function create()
    {
        $name = '_create' . $this->type;
        return $this->$name($this->config);
    }

    protected function getTitle($name)
    {
        $name = ucfirst($name);
        return "<label class=\"col-md-2 control-label\"><b>{$name}</b></label>";
    }

    protected function getLabel($name)
    {
        return "<label class=\"col-md-2 control-label\">{$name}</label>";
    }

    protected function getLabel2($name)
    {
        return "<label class=\"control-label\">{$name}</label>";
    }

    protected function getError($config)
    {
        $msg = isset($config['error']) ? $config['error'] : null;
        $re = isset($config['re']) ? $config['re'] : null;
        $msg = $msg ? "<small class=\"text-danger\" style=\"word-break: break-all; white-space: pre-wrap; \">{$msg}</small> " : "";
        $re = $re ? " <small class=\"text-muted\" style=\"word-break: break-all; white-space: pre-wrap; \">(" . __('jc.' . $re) . ")</small>" : "";
        return $re . $msg;
    }

    protected function addActionButton($parent, $config)
    {
        if (!empty($parent)) {
            foreach ($parent as $v) {
                $field[] = $v['field'];
            }
        }

        if ($config !== null && isset($config['field']) && end($field) != $config['field']) {
            $field[] = $config['field'];
        }

        $field = implode('_', $field);
        return '<button type="button" class="actionButton" act="addField" field="' . $field . '">+</button><button type="button" class="actionButton" act="delField" field="' . $field . '">-</button>';
    }

    public function startPreprocessing($json)
    {
        foreach ($this->config as $c) {
            if ($c['valType'] == 'textareaArray') {
                $vs = $json[$c['field']] ?? [];
                // $vs = explode("\n", $v);

                $columns = $c['columns'][0];
                $new = [];
                foreach ($vs as $kk => $vv) {
                    if (empty($vv)) {
                        continue;
                    }
                    $rows = [];
                    foreach ($columns as $kkk => $vvv) {
                        $rows[] = $vv[$vvv['field']] ?? '';
                    }
                    $new[] = implode(',', $rows);
                }
                $json[$c['field']] = implode("\n", $new);
            }
        }
        return $json;
    }

    public function cancelPreprocessing($json)
    {
        foreach ($this->config as $c) {
            if ($c['valType'] == 'textareaArray') {
                $v = $json[$c['field']] ?? '';
                $vs = explode("\n", $v);

                $columns = $c['columns'][0];
                $new = [];
                foreach ($vs as $kk => $vv) {
                    if (empty($vv)) {
                        continue;
                    }
                    $vvs = explode(',', $vv);
                    $rows = [];
                    foreach ($columns as $kkk => $vvv) {
                        $rows[$vvv['field']] = trim($vvs[$kkk]);
                    }
                    $new[] = $rows;
                }
                $json[$c['field']] = $new;
            }
        }
        return $json;
    }

    protected function fetchArrayTextField($parent, $config, $index = null)
    {
        $field = [];
        if (!empty($this->postField)) {
            $field[] = $this->postField;
        }

        if (!empty($parent)) {
            foreach ($parent as $v) {
                $field[] = $v['field'];
            }
        }

        if ($index !== null) {
            $field[] = "{$index}";
        }

        if ($config !== null && isset($config['field']) && end($field) != $config['field']) {
            $field[] = $config['field'];
        }

        if (count($field) == 1) {
            $fieldStr = current($field);
        } else {
            $fieldStr = '';
            foreach ($field as $k => $f) {
                $fieldStr .= $k == 0 ? $f : "[{$f}]";
            }
        }

        $config['valType'] = $config['valType'] ?? '';
        if ($config['valType'] == 'select') {
            $html = '<select name="' . $fieldStr . '" data-field="' . $fieldStr . '" class="form-control">';
            foreach ($config['options'] as $k => $v) {
                $html .= '<option value="' . $k . '" ' . ($k == $config['val'] ? 'selected' : '') . '>' . $v . '</option>';
            }
            $html .= '</select>';
            return $html;
        } else if ($config['valType'] == 'textarea') {
            return '<textarea class="form-control" name="' . $fieldStr . '" data-field="' . $fieldStr . '" placeholder="' . $config['name'] . '">' . $config['val'] . '</textarea>';
        } else if ($config['valType'] == 'textareaArray') {
            return '<textarea class="form-control" name="' . $fieldStr . '" data-field="' . $fieldStr . '" placeholder="' . $config['name'] . '" style="height:300px;">' . $config['val'] . '</textarea>';
        } else {
            return '<input type="text" class="form-control" name="' . $fieldStr . '" data-field="' . $fieldStr . '" value="' . $config['val'] . '" placeholder="' . $config['name'] . '"/>';
        }
    }
}

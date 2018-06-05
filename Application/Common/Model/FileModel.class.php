<?php
namespace Common\Model;

/**
 * Class FileModel
 * @package Common\Model
 * 文件模型
 */
class FileModel extends BaseModel {

    /**
     * 获取一张图片的链接地址
     * @param $id
     * @param string $default_path
     * @return string
     */
    public function getOnePath($id,$default_path = ''){
        $path = $this->where(array('id'=>$id))->getField('path');
        return $path?C('API_URL').$path:$default_path;
    }

    /**
     * 同时获取多张图片链接地址
     * @param $id_arr
     * @param $default_path
     * @return array
     */
    public function getListPath($id_arr,$default_path){
        $file_list = array();
        foreach($id_arr as $k => $v){
            $path = $this->where(array('id'=>$v))->getField('path');
            $file_list[$k]['path'] = $path?C('API_URL').$path:$default_path;
        }
        return $file_list;
    }
}
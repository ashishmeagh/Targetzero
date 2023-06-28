<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 19/02/2016
 * Time: 12:20
 */

namespace app\models;


use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class Import extends Model
{
    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }
}
?>
<?
namespace Iqsms\Sender;

use Bitrix\Main\Application;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Loader;



Class TemplateTable extends Entity\DataManager
{

    public static
    function getFilePath()
    {
        return __FILE__;
    }

    public static
    function getTableName()
    {
        return 'iqsms_sender_template';
    }

    public static
    function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary'      => true,
                'autocomplete' => true
            )),
            new Entity\StringField('TYPE', array(
                'required' => true
            )),
            new Entity\StringField('NAME', array(
                'required' => true
            )),
            new Entity\BooleanField('ACTIVE'),

            new Entity\StringField('PHONE', array(
                'required' => true
            )),
            new Entity\StringField('PHONE_COPY'),
            new Entity\StringField('TEXT', array(
                'required' => true
            )),
            new Entity\BooleanField('EVENT', array(
            	'default' => false
			)),
            new Entity\ReferenceField('SITE', '\Iqsms\Sender\Template\SiteTable', array('=this.ID' => 'ref.TID'), array('type_join' => 'left'))
        );
    }
}
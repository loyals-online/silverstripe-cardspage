<?php

namespace Loyals\CardsPage\Model;

use Page;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use UncleCheese\DisplayLogic\Forms\Wrapper;

/**
 * Created by PhpStorm.
 * User: jpvanderpoel
 * Date: 14/10/16
 * Time: 13:20
 */
class PageCard extends DataObject
{
    private static $table_name = 'PageCard';
    private static $singular_name = 'Page Card';
    private static $plural_name = 'Page Cards';

    private static $db = [
        'Name'          => 'Varchar(255)',
        'Title'         => 'Varchar(255)',
        'SubTitle'      => 'Varchar(255)',
        'ContentType'   => 'Enum("Text,Image","Text")',
        'Content'       => 'HTMLText',
        'SimpleContent' => 'Text',
        'LinkType'      => 'Enum("None,Internal,External,Email,Telephone","None")',
        'LinkExternal'  => 'Varchar(255)',
        'LinkEmail'     => 'Varchar(255)',
        'LinkTelephone' => 'Varchar(255)',

    ];

    private static $has_one = [
        'Image'        => Image::class,
        'ContentImage' => Image::class,
        'Page'         => Page::class,
    ];

    private static $summary_fields = [
        'Name' => 'Name',
    ];

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'LinkExternal',
            'Content',
            'ContentImage',
            'SimpleContent',
            'Image',
            'PageID',
            'LinkEmail',
            'LinkTelephone',
        ]);

        $fields->insertAfter(
            UploadField::create('Image', _t('PageCard.Image', 'Image'))
                ->setFolderName('pagecard-images'),
            'SubTitle'
        );

        $fields->changeFieldOrder([
            'Name',
            'Title',
            'SubTitle',
            'Image',
            'ContentType',
            'LinkType',
            'Content',
            'SimpleContent',
        ]);

        $fields->insertAfter(
            Wrapper::create(
                Wrapper::create(
                    UploadField::create('ContentImage', _t('PageCard.ContentImage', 'Foreground Image'))
                        ->setFolderName('pagecard-images')
                )
                    ->displayIf('ContentType')
                    ->isEqualTo('Image')
                    ->end(),
                Wrapper::create(
                    Wrapper::create(
                        HtmlEditorField::create('Content', _t('PageCard.Content', 'Content'))
                            ->setRows(10)
                    )
                        ->displayIf('LinkType')
                        ->isEqualTo('None')
                        ->end(),
                    Wrapper::create(
                        TextareaField::create('SimpleContent', _t('PageCard.Content', 'Content'))
                            ->setRows(15)
                    )
                        ->displayIf('LinkType')
                        ->isNotEqualTo('None')
                        ->end()
                )
                    ->displayIf('ContentType')
                    ->isEqualTo('Text')
                    ->end()
            )
                ->setName('ContentWrapper'),
            'ContentType'
        );

        $fields->insertAfter(
            Wrapper::create(
                Wrapper::create(
                    TreeDropdownField::create(
                        'PageID',
                        _t('PageCard.LinkInternal', 'Link to internal page'),
                        'SiteTree',
                        'ID',
                        'MenuTitle'
                    )
                )
                    ->displayIf('LinkType')
                    ->isEqualTo('Internal')
                    ->end(),
                Wrapper::create(
                    TextField::create('LinkExternal', _t('PageCard.LinkExternal', 'Link to external page'))
                )
                    ->displayIf('LinkType')
                    ->isEqualTo('External')
                    ->end(),
                Wrapper::create(
                    TextField::create('LinkEmail', _t('PageCard.LinkEmail', 'Link to email address'))
                )
                    ->displayIf('LinkType')
                    ->isEqualTo('Email')
                    ->end(),
                Wrapper::create(
                    TextField::create('LinkTelephone', _t('PageCard.LinkTelephone', 'Link to telephone number'))
                )
                    ->displayIf('LinkType')
                    ->isEqualTo('Telephone')
                    ->end()
            )
                ->setName('LinkWrapper'),
            'LinkType'
        );

        $this->extend('modifyCMSFields', $fields);

        return $fields;
    }

    public function getDataLink()
    {
        switch ($this->LinkType) {
            case 'Internal':
                return $this->Page()->Link();
            case 'External':
                return $this->LinkExternal;
            case 'Email':
                return 'mailto:' . $this->LinkEmail;
            case 'Telephone':
                return 'tel:' . $this->LinkTelephone;
        }
    }
}


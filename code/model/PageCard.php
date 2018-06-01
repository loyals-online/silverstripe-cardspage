<?php

/**
 * Created by PhpStorm.
 * User: jpvanderpoel
 * Date: 14/10/16
 * Time: 13:20
 */
class PageCard extends DataObject
{

    static $singular_name = 'Page Card';
    static $plural_name   = 'Page Cards';

    static $db = [
        'Name'          => 'Varchar(255)',
        'Title'         => 'Varchar(255)',
        'SubTitle'      => 'Varchar(255)',
        'ContentType'   => 'Enum("Text,Image","Text")',
        'Content'       => 'CustomHTMLText',
        'SimpleContent' => 'Text',
        'LinkType'      => 'Enum("None,Internal,External,Email,Telephone","None")',
        'LinkExternal'  => 'Varchar(255)',
        'LinkEmail'     => 'Varchar(255)',
        'LinkTelephone' => 'Varchar(255)',

    ];

    private static $has_one = [
        'Image'        => 'Image',
        'ContentImage' => 'Image',
        'Page'         => 'Page',
    ];

    public static $summary_fields = [
        'Name' => 'Name',
    ];

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canView($member = null) {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canEdit($member = null) {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canDelete($member = null) {
        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canCreate($member = null) {
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
                ->setFolderName('pagecard-images')
                ->setDisplayFolderName('pagecard-images'),
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
            DisplayLogicWrapper::create(
                DisplayLogicWrapper::create(
                    UploadField::create('ContentImage', _t('PageCard.ContentImage', 'Foreground Image'))
                        ->setFolderName('pagecard-images')
                        ->setDisplayFolderName('pagecard-images')
                )
                    ->displayIf('ContentType')
                    ->isEqualTo('Image')
                    ->end(),
                DisplayLogicWrapper::create(
                    DisplayLogicWrapper::create(
                        CustomHtmlEditorField::create('Content', _t('PageCard.Content', 'Content'))
                            ->setRows(10)
                    )
                        ->displayIf('LinkType')
                        ->isEqualTo('None')
                        ->end(),
                    DisplayLogicWrapper::create(
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
            DisplayLogicWrapper::create(
                DisplayLogicWrapper::create(
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
                DisplayLogicWrapper::create(
                    TextField::create('LinkExternal', _t('PageCard.LinkExternal', 'Link to external page'))
                )
                    ->displayIf('LinkType')
                    ->isEqualTo('External')
                    ->end(),
                DisplayLogicWrapper::create(
                    TextField::create('LinkEmail', _t('PageCard.LinkEmail', 'Link to email address'))
                )
                    ->displayIf('LinkType')
                    ->isEqualTo('Email')
                    ->end(),
                DisplayLogicWrapper::create(
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
                return $this->Page()
                    ->Link();
                break;
            case 'External':
                return $this->LinkExternal;
                break;
            case 'Email':
                return 'mailto:' . $this->LinkEmail;
                break;
            case 'Telephone':
                return 'tel:' . $this->LinkTelephone;
                break;
        }
    }
}


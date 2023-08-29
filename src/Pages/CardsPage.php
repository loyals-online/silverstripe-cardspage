<?php

namespace Loyals\CardsPage\Pages;

use Page;
use Loyals\CardsPage\Controllers\CardsPageController;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\GridField\GridField;

class CardsPage extends Page
{
    private static $singular_name = 'Cards Page';
    private static $plural_name = 'Cards Pages';

    private static $db = [
        'CardsEnabled' => 'Boolean',
    ];

    private static $many_many = [
        'Cards' => 'PageCard',
    ];

    private static $many_many_extraFields = [
        'Cards' => ['SortOrder' => 'Int'],
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RecordEditor::create()
            ->removeComponentsByType('GridFieldDeleteAction')
            ->addComponent(new GridFieldDeleteAction(false))
            ->addComponent(new GridFieldOrderableRows('SortOrder'));

        $fields->addFieldsToTab("Root.Cards", [
            FieldGroup::create(CheckboxField::create('CardsEnabled', ''))->setTitle(_t('CardsPage.CardsEnabled', 'Enable Cards')),
            GridField::create('Cards', _t('CardsPage.PageCards', 'Page Cards'), $this->Cards()->sort('SortOrder ASC'), $config),
        ]);

        $this->extend('modifyCMSFields', $fields);

        return $fields;
    }

    public function getControllerName()
    {
        return CardsPageController::class;
    }
}

<?php

class CardsPage extends Page
{

    static $singular_name = 'Cards Page';
    static $plural_name = 'Cards Pages';

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
            GridField::create('Cards', _t('CardsPage.PageCards', 'Page Cards'), $this->owner->Cards()
                ->sort('SortOrder ASC'), $config),
        ]);

        return $fields;
    }
}

/**
 * Class CardsPage_Controller
 *
 * @property CardsPage dataRecord
 * @method CardsPage data()
 * @mixin CardsPage dataRecord
 */
class CardsPage_Controller extends Page_Controller
{
}

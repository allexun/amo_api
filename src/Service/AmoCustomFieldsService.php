<?php

namespace App\Service;

use AmoCRM\Collections\CustomFields\CustomFieldEnumsCollection;
use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\PagesFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CustomFields\EnumModel;
use AmoCRM\Models\CustomFields\MultiselectCustomFieldModel;
use AmoCRM\Models\CustomFieldsValues\MultiselectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultiselectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultiselectCustomFieldValueModel;

class AmoCustomFieldsService
{
    public function __construct(
        private readonly AmoClientProvider $provider,
    ) {
    }

    public function createCustomFieldMultiList(string $name): void
    {
        $fields = $this->provider->getClient()->customFields(EntityTypesInterface::LEADS);

        $code = 'MULTILIST'.strtoupper($name);
        $field = new MultiselectCustomFieldModel();
        $field->setName('Example multi list '.$name);
        $field->setSort(30);
        $field->setCode($code);
        $field->setEnums(
            (new CustomFieldEnumsCollection())
                ->add(
                    (new EnumModel())
                        ->setValue('Value 1')
                        ->setSort(10)
                )
                ->add(
                    (new EnumModel())
                        ->setValue('Value 2')
                        ->setSort(20)
                )
                ->add(
                    (new EnumModel())
                        ->setValue('Value 3')
                        ->setSort(30)
                )
        );

        $fieldsCollection = new CustomFieldsCollection();
        $fieldsCollection->add($field);

        try {
            $fields->add($fieldsCollection);
            $this->updateAllCustomFieldMultiList($code, 'Value 2');
        } catch (AmoCRMApiException $e) {
            ErrorPrinter::printError($e);
            exit;
        }
    }

    public function updateAllCustomFieldMultiList(string $code, string $value): void
    {
        $leads = $this->provider->getClient()->leads();
        $page = 1;
        $filter = new PagesFilter();
        $filter->setLimit(100);
        $filter->setPage($page);

        while (true) {
            try {
                // var_dump($page);
                $leadsCollection = $leads->get($filter);

                foreach ($leadsCollection as $lead) {
                    $leadCustomFieldsValues = new CustomFieldsValuesCollection();
                    $customValues = new MultiselectCustomFieldValuesModel();
                    $customValues->setFieldCode($code);
                    $customValues->setValues(
                        (new MultiselectCustomFieldValueCollection())
                            ->add((new MultiselectCustomFieldValueModel())->setValue($value))
                    );
                    $leadCustomFieldsValues->add($customValues);

                    $lead->setCustomFieldsValues($leadCustomFieldsValues);

                    // var_dump($lead->getName());
                    // var_dump($lead->getCustomFieldsValues());
                }

                $leadsCollection = $leads->update($leadsCollection);

                $filter->setPage(++$page);

                sleep(1);
            } catch (AmoCRMApiException $e) {
                if (204 === $e->getCode()) {
                    break;
                }

                throw $e;
            }
        }
    }
}

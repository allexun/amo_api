<?php

namespace App\Service;

use AmoCRM\Collections\CustomFields\CustomFieldEnumsCollection;
use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CustomFields\EnumModel;
use AmoCRM\Models\CustomFields\MultiselectCustomFieldModel;

class AmoCustomFieldsService
{
    public function __construct(
        private readonly AmoClientProvider $provider,
    ) {
    }

    public function createCustomFieldMultiList(): void
    {
        $fields = $this->provider->getClient()->customFields(EntityTypesInterface::LEADS);

        $id = random_int(0, 10000);
        $field = new MultiselectCustomFieldModel();
        $field->setName('Example multi list '.$id);
        $field->setSort(30);
        $field->setCode('MULTILIST'.$id);
        $field->setEnums(
            (new CustomFieldEnumsCollection())
                ->add(
                    (new EnumModel())
                        ->setValue('Value '.random_int(1, 10000))
                        ->setSort(10)
                )
                ->add(
                    (new EnumModel())
                        ->setValue('Value '.random_int(1, 10000))
                        ->setSort(20)
                )
                ->add(
                    (new EnumModel())
                        ->setValue('Value '.random_int(1, 10000))
                        ->setSort(30)
                )
        );

        $fieldsCollection = new CustomFieldsCollection();
        $fieldsCollection->add($field);

        try {
            $fields->add($fieldsCollection);
        } catch (AmoCRMApiException $e) {
            ErrorPrinter::printError($e);
            exit;
        }
    }
}

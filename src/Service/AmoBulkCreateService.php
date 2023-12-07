<?php

namespace App\Service;

use AmoCRM\Collections\CompaniesCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;

class AmoBulkCreateService
{
    public function __construct(
        private readonly AmoClientProvider $provider,
    ) {
    }

    public function create1000Models(): int
    {
        $count = 0;

        for ($i = 0; $i < 10; ++$i) {
            $count += $this->createBatch(100);
            sleep(1);
        }

        return $count;
    }

    public function createBatch(int $size): int
    {
        $companiesCollection = new CompaniesCollection();
        $contactsCollection = new ContactsCollection();
        $leadsCollection = new LeadsCollection();

        for ($i = 0; $i < $size; ++$i) {
            $company = new CompanyModel();
            $company->setName('Example Company '.random_int(0, 100000));
            $companiesCollection->add($company);

            $contact = new ContactModel();
            $contact->setName('Example Contact '.random_int(0, 100000));
            $contactsCollection->add($contact);

            $lead = new LeadModel();
            $lead->setName('Example lead '.random_int(0, 100000));
            $lead->setCompany($company);
            $leadContacts = new ContactsCollection();
            $leadContacts->add($contact);
            $lead->setContacts($leadContacts);
            $leadsCollection->add($lead);
        }

        $companiesCollection = $this->provider->getClient()->companies()->add($companiesCollection);
        $companiesCollection = $this->provider->getClient()->contacts()->add($contactsCollection);
        $companiesCollection = $this->provider->getClient()->leads()->add($leadsCollection);

        return $companiesCollection->count();
    }
}

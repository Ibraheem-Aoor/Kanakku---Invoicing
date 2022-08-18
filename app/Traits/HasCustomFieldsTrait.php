<?php

namespace Kanakku\Traits;


use Carbon\Carbon;
use Kanakku\Models\CustomField;
use Kanakku\Models\CustomFieldValue;

trait HasCustomFieldsTrait
{
    public function fields()
    {
        return $this->morphMany('Kanakku\Models\CustomFieldValue', 'custom_field_valuable');
    }

    public function addCustomFields($customFields)
    {
        foreach ($customFields as $field) {
            $customField = CustomField::find($field['id']);

            $customFieldValue = [
                'type' => $customField->type,
                'custom_field_id' => $customField->id,
                'company_id' => $customField->company_id,
                getCustomFieldValueKey($customField->type) => $field['value']
            ];

            $this->fields()->create($customFieldValue);
        }
    }

    public function updateCustomFields($customFields)
    {
        foreach ($customFields as $field) {
            $customField = CustomField::find($field['id']);
            $customFieldValue = $this->fields()->firstOrCreate([
                'custom_field_id' => $customField->id,
                'type' => $customField->type,
                'company_id' => $this->company_id
            ]);

            $type = getCustomFieldValueKey($customField->type);
            $customFieldValue->$type = $field['value'];
            $customFieldValue->save();
        }
    }
}
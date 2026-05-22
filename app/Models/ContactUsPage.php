<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsPage extends Model
{
    use HasFactory;

    protected $table = 'contact_us_page';

    protected $fillable = [
        'section_key',
        'title',
        'paragraphs',
        'list_items_text',
        'phone_numbers_text',
        'email_addresses_text',
        'social_links_text',
        'address',
        'map_embed_url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeBySection($query, $sectionKey)
    {
        return $query->where('section_key', $sectionKey);
    }

    /**
     * Get list items as array (backward compatibility)
     */
    public function getListItemsAttribute()
    {
        if ($this->list_items_text) {
            return explode("\n", $this->list_items_text);
        }
        return [];
    }

    /**
     * Get phone numbers as array (backward compatibility)
     */
    public function getPhoneNumbersAttribute()
    {
        if ($this->phone_numbers_text) {
            return explode("\n", $this->phone_numbers_text);
        }
        return [];
    }

    /**
     * Get email addresses as array (backward compatibility)
     */
    public function getEmailAddressesAttribute()
    {
        if ($this->email_addresses_text) {
            return explode("\n", $this->email_addresses_text);
        }
        return [];
    }

    /**
     * Get social links as array (backward compatibility)
     */
    public function getSocialLinksAttribute()
    {
        if ($this->social_links_text) {
            $links = json_decode($this->social_links_text, true);
            return is_array($links) ? $links : explode("\n", $this->social_links_text);
        }
        return [];
    }
}

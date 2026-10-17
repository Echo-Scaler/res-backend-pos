<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'logo',
        'cover_image',
        'phone',
        'secondary_phone',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'tax_id',
        'receipt_header',
        'receipt_footer',
        'show_logo_on_receipt',
        'show_tax_id_on_receipt',
        'opening_time',
        'closing_time',
        'operating_days',
        'is_dine_in_enabled',
        'is_takeaway_enabled',
        'is_qr_order_enabled',
        'currency',
        'currency_symbol',
        'default_tax_percent',
        'default_service_charge_percent',
        'is_tax_enabled',
        'tax_name',
        'tax_calculation_mode',
        'tax_office_name',
        'apply_tax_dine_in',
        'apply_tax_takeaway',
        'apply_tax_delivery',
        'apply_tax_qr_order',
        'is_service_charge_enabled',
        'service_charge_name',
        'is_service_charge_taxable',
        'service_charge_dine_in_only',
        'takeaway_packaging_fee',
        'rounding_method',
        'rounding_direction',
        'round_cash_only',
        'show_tax_breakdown_on_receipt',
        'show_service_charge_on_receipt',
        'print_tax_stamp_seal',
        'tax_disclaimer_text',
        'printer_paper_width',
        'auto_kick_drawer',
        'auto_print_receipt',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_logo_on_receipt' => 'boolean',
            'show_tax_id_on_receipt' => 'boolean',
            'is_dine_in_enabled' => 'boolean',
            'is_takeaway_enabled' => 'boolean',
            'is_qr_order_enabled' => 'boolean',
            'auto_kick_drawer' => 'boolean',
            'auto_print_receipt' => 'boolean',
            'operating_days' => 'array',
            'default_tax_percent' => 'float',
            'default_service_charge_percent' => 'float',
            'is_tax_enabled' => 'boolean',
            'apply_tax_dine_in' => 'boolean',
            'apply_tax_takeaway' => 'boolean',
            'apply_tax_delivery' => 'boolean',
            'apply_tax_qr_order' => 'boolean',
            'is_service_charge_enabled' => 'boolean',
            'is_service_charge_taxable' => 'boolean',
            'service_charge_dine_in_only' => 'boolean',
            'takeaway_packaging_fee' => 'integer',
            'round_cash_only' => 'boolean',
            'show_tax_breakdown_on_receipt' => 'boolean',
            'show_service_charge_on_receipt' => 'boolean',
            'print_tax_stamp_seal' => 'boolean',
        ];
    }

    /**
     * Accessor for full logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        return Storage::url($this->logo);
    }

    /**
     * Accessor for full cover image URL.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            return $this->cover_image;
        }

        return Storage::url($this->cover_image);
    }

    /**
     * Check if restaurant is operating on a specific day code (e.g. 'MON', 'TUE').
     */
    public function operatesOn(string $dayCode): bool
    {
        if (empty($this->operating_days)) {
            return true;
        }

        return in_array(strtoupper($dayCode), $this->operating_days, true);
    }

    /**
     * Get the users that belong to this restaurant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the owner user of this restaurant.
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'OWNER');
        });
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    public function recurringExpenses(): HasMany
    {
        return $this->hasMany(RecurringExpense::class);
    }

    public function expenseBudgets(): HasMany
    {
        return $this->hasMany(ExpenseBudget::class);
    }

    public function expenseTransactions(): HasMany
    {
        return $this->hasMany(ExpenseTransaction::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function diningTables(): HasMany
    {
        return $this->hasMany(DiningTable::class);
    }
}

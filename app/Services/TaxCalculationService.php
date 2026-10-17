<?php

namespace App\Services;

use App\Models\Restaurant;

class TaxCalculationService
{
    /**
     * Calculate comprehensive bill breakdown including Commercial Tax,
     * Service Charge, Packaging fee, and MMK Cash Rounding.
     *
     * @param  int  $subtotal  Base order amount in MMK
     * @param  string  $channel  'dine_in'|'takeaway'|'delivery'|'qr_order'
     * @param  string  $paymentMethod  'cash'|'digital'|'card'
     * @param  Restaurant|null  $restaurant  The restaurant model with configuration
     * @return array<string, mixed>
     */
    public function calculate(
        int $subtotal,
        string $channel = 'dine_in',
        string $paymentMethod = 'cash',
        ?Restaurant $restaurant = null
    ): array {
        // Fallback default values if no restaurant is provided
        $isTaxEnabled = $restaurant ? (bool) $restaurant->is_tax_enabled : true;
        $taxRate = $restaurant ? (float) $restaurant->default_tax_percent : 5.0;
        $taxMode = $restaurant ? (string) ($restaurant->tax_calculation_mode ?: 'inclusive') : 'inclusive';
        $taxName = $restaurant ? (string) ($restaurant->tax_name ?: 'Commercial Tax') : 'Commercial Tax';

        $isServiceEnabled = $restaurant ? (bool) $restaurant->is_service_charge_enabled : true;
        $serviceRate = $restaurant ? (float) $restaurant->default_service_charge_percent : 10.0;
        $serviceName = $restaurant ? (string) ($restaurant->service_charge_name ?: 'Service Charge') : 'Service Charge';
        $serviceDineInOnly = $restaurant ? (bool) $restaurant->service_charge_dine_in_only : true;
        $isServiceTaxable = $restaurant ? (bool) $restaurant->is_service_charge_taxable : false;

        $packagingFee = 0;
        if (in_array($channel, ['takeaway', 'delivery'])) {
            $packagingFee = $restaurant ? (int) $restaurant->takeaway_packaging_fee : 0;
        }

        $roundingMethod = $restaurant ? (string) ($restaurant->rounding_method ?: 'round_50') : 'round_50';
        $roundingDirection = $restaurant ? (string) ($restaurant->rounding_direction ?: 'nearest') : 'nearest';
        $roundCashOnly = $restaurant ? (bool) $restaurant->round_cash_only : true;

        // 1. Service Charge Calculation
        $serviceChargeAmount = 0;
        $appliesServiceCharge = false;

        if ($isServiceEnabled) {
            if ($serviceDineInOnly) {
                $appliesServiceCharge = ($channel === 'dine_in');
            } else {
                $appliesServiceCharge = true;
            }

            if ($appliesServiceCharge && $serviceRate > 0) {
                $serviceChargeAmount = (int) round($subtotal * ($serviceRate / 100));
            }
        }

        // 2. Tax Applicability
        $taxApplicableForChannel = true;
        if ($restaurant) {
            $taxApplicableForChannel = match ($channel) {
                'dine_in' => $restaurant->apply_tax_dine_in ?? true,
                'takeaway' => $restaurant->apply_tax_takeaway ?? true,
                'delivery' => $restaurant->apply_tax_delivery ?? true,
                'qr_order' => $restaurant->apply_tax_qr_order ?? true,
                default => true,
            };
        }

        $taxAmount = 0;
        $baseWithoutTax = $subtotal;
        $taxableAmount = $subtotal;

        if ($isTaxEnabled && $taxApplicableForChannel && $taxRate > 0) {
            if ($isServiceTaxable && $serviceChargeAmount > 0) {
                $taxableAmount = $subtotal + $serviceChargeAmount;
            }

            if ($taxMode === 'inclusive') {
                // Inclusive: Subtotal already embeds the tax
                $baseWithoutTax = (int) round($taxableAmount / (1 + ($taxRate / 100)));
                $taxAmount = $taxableAmount - $baseWithoutTax;
                $preRoundingTotal = $subtotal + $serviceChargeAmount + $packagingFee;
            } else {
                // Exclusive: Tax is added on top of taxable amount
                $taxAmount = (int) round($taxableAmount * ($taxRate / 100));
                $preRoundingTotal = $subtotal + $serviceChargeAmount + $taxAmount + $packagingFee;
            }
        } else {
            $preRoundingTotal = $subtotal + $serviceChargeAmount + $packagingFee;
        }

        // 3. Cash Rounding Calculation (Rule 6: MMK)
        $shouldRound = true;
        if ($roundCashOnly && $paymentMethod !== 'cash') {
            $shouldRound = false;
        }

        $roundedTotal = $preRoundingTotal;
        $roundingDifference = 0;

        if ($shouldRound && $roundingMethod !== 'none') {
            $unit = ($roundingMethod === 'round_100') ? 100 : 50;

            $roundedTotal = match ($roundingDirection) {
                'ceil' => (int) (ceil($preRoundingTotal / $unit) * $unit),
                'floor' => (int) (floor($preRoundingTotal / $unit) * $unit),
                default => (int) (round($preRoundingTotal / $unit) * $unit),
            };

            $roundingDifference = $roundedTotal - $preRoundingTotal;
        }

        return [
            'subtotal' => $subtotal,
            'subtotal_formatted' => number_format($subtotal, 0).' MMK',
            'channel' => $channel,
            'payment_method' => $paymentMethod,
            'is_tax_enabled' => $isTaxEnabled,
            'tax_name' => $taxName,
            'tax_rate_percent' => $taxRate,
            'tax_calculation_mode' => $taxMode,
            'is_tax_inclusive' => ($taxMode === 'inclusive'),
            'taxable_amount' => $taxableAmount,
            'tax_amount' => $taxAmount,
            'tax_amount_formatted' => number_format($taxAmount, 0).' MMK',
            'is_service_charge_enabled' => $isServiceEnabled,
            'service_charge_name' => $serviceName,
            'service_charge_rate_percent' => $serviceRate,
            'service_charge_amount' => $serviceChargeAmount,
            'service_charge_amount_formatted' => number_format($serviceChargeAmount, 0).' MMK',
            'packaging_fee' => $packagingFee,
            'packaging_fee_formatted' => number_format($packagingFee, 0).' MMK',
            'pre_rounding_total' => $preRoundingTotal,
            'rounding_method' => $roundingMethod,
            'rounding_difference' => $roundingDifference,
            'rounding_difference_formatted' => ($roundingDifference >= 0 ? '+' : '').number_format($roundingDifference, 0).' MMK',
            'final_total' => $roundedTotal,
            'final_total_formatted' => number_format($roundedTotal, 0).' MMK',
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
        ];
    }
}

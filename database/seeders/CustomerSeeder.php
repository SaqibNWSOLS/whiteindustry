<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            // Corporate customers
            [
                'type' => 'customer',
                'company_name' => 'Tech Solutions Inc.',
                'industry_type' => 'Information Technology',
                'tax_id' => 'TX-123456789',
                'contact_person' => 'John Smith',
                'email' => 'john.smith@techsolutions.com',
                'phone' => '+1-555-0101',
                'address' => '123 Tech Park Drive',
                'city' => 'San Francisco',
                'postal_code' => '94105',
                'status' => 'active',
            ],
            [
                'type' => 'customer',
                'company_name' => 'Global Manufacturing Ltd.',
                'industry_type' => 'Manufacturing',
                'tax_id' => 'TX-987654321',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah.j@globalmfg.com',
                'phone' => '+1-555-0102',
                'address' => '456 Industrial Avenue',
                'city' => 'Chicago',
                'postal_code' => '60601',
                'status' => 'active',
            ],
            [
                'type' => 'customer',
                'company_name' => 'Premium Retail Group',
                'industry_type' => 'Retail',
                'tax_id' => 'TX-456789123',
                'contact_person' => 'Mike Wilson',
                'email' => 'mike.wilson@premiumretail.com',
                'phone' => '+1-555-0103',
                'address' => '789 Market Street',
                'city' => 'New York',
                'postal_code' => '10001',
                'status' => 'active',
            ],

            // Individual customers
            [
                'type' => 'lead',
                'company_name' => null,
                'industry_type' => null,
                'tax_id' => 'TX-111222333',
                'contact_person' => 'David Brown',
                'email' => 'david.brown@email.com',
                'phone' => '+1-555-0104',
                'address' => '321 Oak Lane',
                'city' => 'Austin',
                'postal_code' => '73301',
                'status' => 'active',
            ],
            [
                'type' => 'lead',
                'company_name' => null,
                'industry_type' => null,
                'tax_id' => 'TX-444555666',
                'contact_person' => 'Emily Davis',
                'email' => 'emily.davis@email.com',
                'phone' => '+1-555-0105',
                'address' => '654 Pine Street',
                'city' => 'Seattle',
                'postal_code' => '98101',
                'status' => 'active',
            ],

            // More customer customers from various industries
            [
                'type' => 'customer',
                'company_name' => 'Healthcare Partners LLC',
                'industry_type' => 'Healthcare',
                'tax_id' => 'TX-777888999',
                'contact_person' => 'Dr. Robert Chen',
                'email' => 'r.chen@healthcarepartners.com',
                'phone' => '+1-555-0106',
                'address' => '987 Medical Center Drive',
                'city' => 'Boston',
                'postal_code' => '02115',
                'status' => 'active',
            ],
            [
                'type' => 'customer',
                'company_name' => 'Eco Foods International',
                'industry_type' => 'Food & Beverage',
                'tax_id' => 'TX-222333444',
                'contact_person' => 'Maria Garcia',
                'email' => 'maria.garcia@ecofoods.com',
                'phone' => '+1-555-0107',
                'address' => '147 Farm-to-Market Road',
                'city' => 'Portland',
                'postal_code' => '97205',
                'status' => 'active',
            ],
            [
                'type' => 'customer',
                'company_name' => 'Innovate Construction Co.',
                'industry_type' => 'Construction',
                'tax_id' => 'TX-555666777',
                'contact_person' => 'James Anderson',
                'email' => 'j.anderson@innovateconstruction.com',
                'phone' => '+1-555-0108',
                'address' => '258 Builders Way',
                'city' => 'Denver',
                'postal_code' => '80202',
                'status' => 'active',
            ],

            // More person customers
            [
                'type' => 'lead',
                'company_name' => null,
                'industry_type' => null,
                'tax_id' => 'TX-888999000',
                'contact_person' => 'Lisa Thompson',
                'email' => 'lisa.thompson@email.com',
                'phone' => '+1-555-0109',
                'address' => '753 Cedar Avenue',
                'city' => 'Miami',
                'postal_code' => '33101',
                'status' => 'active',
            ],
            [
                'type' => 'lead',
                'company_name' => null,
                'industry_type' => null,
                'tax_id' => 'TX-333444555',
                'contact_person' => 'Kevin Martinez',
                'email' => 'k.martinez@email.com',
                'phone' => '+1-555-0110',
                'address' => '159 Maple Road',
                'city' => 'Phoenix',
                'postal_code' => '85001',
                'status' => 'active',
            ],

            // Inactive customer example
            [
                'type' => 'customer',
                'company_name' => 'Former Partner Corp',
                'industry_type' => 'Consulting',
                'tax_id' => 'TX-666777888',
                'contact_person' => 'Thomas Wright',
                'email' => 'thomas.w@formerpartner.com',
                'phone' => '+1-555-0111',
                'address' => '864 Past Business Lane',
                'city' => 'Dallas',
                'postal_code' => '75201',
                'status' => 'inactive',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $this->command->info('Successfully seeded ' . count($customers) . ' customers.');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Site;
use App\Models\Location;
use App\Models\MaterialCategory;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\QrCode;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

class BulalacaoLogisticsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles Check
        $adminRole = Role::firstOrCreate(['slug' => 'administrator'], ['name' => 'Administrator', 'description' => 'Full administrative access and system governance']);
        $officerRole = Role::firstOrCreate(['slug' => 'inventory-officer'], ['name' => 'Inventory Officer', 'description' => 'Manages materials, QR code batching, warehouse tiers, and transaction audits']);
        $siteRole = Role::firstOrCreate(['slug' => 'site-personnel'], ['name' => 'Site Personnel', 'description' => 'Scans QR codes on site, receives dispatches, and handles material movement']);
        $pmRole = Role::firstOrCreate(['slug' => 'project-manager'], ['name' => 'Project Manager', 'description' => 'Monitors material consumption, stock thresholds, progress reports, and approvals']);

        // 2. Demo Users
        $admin = User::firstOrCreate(['email' => 'admin@logistic.app'], [
            'name' => 'System Admin (Bulalacao Central)',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'phone' => '+63 (917) 555-0100',
            'status' => 'active',
        ]);

        $officer = User::firstOrCreate(['email' => 'inventory@logistic.app'], [
            'name' => 'Engr. Marco Hernandez',
            'password' => Hash::make('password'),
            'role_id' => $officerRole->id,
            'phone' => '+63 (917) 555-0102',
            'status' => 'active',
        ]);

        $siteUser = User::firstOrCreate(['email' => 'site@logistic.app'], [
            'name' => 'Ronald Dela Cruz',
            'password' => Hash::make('password'),
            'role_id' => $siteRole->id,
            'phone' => '+63 (917) 555-0103',
            'status' => 'active',
        ]);

        $pm = User::firstOrCreate(['email' => 'pm@logistic.app'], [
            'name' => 'Arch. Sofia Castillo',
            'password' => Hash::make('password'),
            'role_id' => $pmRole->id,
            'phone' => '+63 (917) 555-0104',
            'status' => 'active',
        ]);

        // 3. Construction Sites in Bulalacao, Oriental Mindoro
        $site1 = Site::updateOrCreate(['name' => 'Bulalacao Municipal Infrastructure Project'], [
            'address' => 'Poblacion & Coastal Highway, Bulalacao, Oriental Mindoro',
            'latitude' => 12.3325,
            'longitude' => 121.3468,
            'description' => 'Main town arterial road concreting and coastal access bypass',
            'status' => 'active',
        ]);

        $site2 = Site::updateOrCreate(['name' => 'Maujao-Campasan Flood Control & Spillway'], [
            'address' => 'Brgy. Maujao, Bulalacao, Oriental Mindoro',
            'latitude' => 12.3160,
            'longitude' => 121.3120,
            'description' => 'River protection dike, slope stabilization, and box culvert spillway',
            'status' => 'active',
        ]);

        // 4. Locations & Storage Areas
        $loc1 = Location::updateOrCreate(['name' => 'Main Warehouse Yard', 'site_id' => $site1->id], [
            'type' => 'warehouse',
            'latitude' => 12.3340,
            'longitude' => 121.3465,
            'description' => 'Primary covered storage depot for bagged cement, tools, and hardware',
            'status' => 'active',
        ]);

        $loc2 = Location::updateOrCreate(['name' => 'San Roque Staging Bay', 'site_id' => $site1->id], [
            'type' => 'storage_area',
            'latitude' => 12.3480,
            'longitude' => 121.3550,
            'description' => 'Open staging yard for steel rebar piles, gravel, and sand stockpiles',
            'status' => 'active',
        ]);

        $loc3 = Location::updateOrCreate(['name' => 'Poblacion Section 2 Worksite', 'site_id' => $site1->id], [
            'type' => 'site_section',
            'latitude' => 12.3295,
            'longitude' => 121.3390,
            'description' => 'Active road paving corridor with on-site material drop-off area',
            'status' => 'active',
        ]);

        $loc4 = Location::updateOrCreate(['name' => 'Maujao Depot & Silo', 'site_id' => $site2->id], [
            'type' => 'warehouse',
            'latitude' => 12.3160,
            'longitude' => 121.3120,
            'description' => 'River dike construction logistics depot and equipment garage',
            'status' => 'active',
        ]);

        // 5. Material Categories
        $catCement = MaterialCategory::firstOrCreate(['name' => 'Cement & Binders'], ['description' => 'Portland, Pozzolan, Hydraulic cements and chemical additives']);
        $catSteel = MaterialCategory::firstOrCreate(['name' => 'Structural Steel & Rebar'], ['description' => 'Deformed steel bars, tie wires, angle bars, and mesh']);
        $catAggregates = MaterialCategory::firstOrCreate(['name' => 'Aggregates & Masonry'], ['description' => 'Crushed gravel, river sand, hollow blocks, and boulders']);
        $catLumber = MaterialCategory::firstOrCreate(['name' => 'Timber & Formwork'], ['description' => 'Coco lumber, marine plywood, scaffoldings, and form ties']);
        $catPlumbing = MaterialCategory::firstOrCreate(['name' => 'Plumbing & Drainage'], ['description' => 'PVC pipes, HDPE pipes, drainage fittings, and valves']);
        $catPPE = MaterialCategory::firstOrCreate(['name' => 'PPE & Safety Equipment'], ['description' => 'Hard hats, safety boots, high-vis vests, harnesses, and cones']);

        // 6. Suppliers
        $sup1 = Supplier::firstOrCreate(['name' => 'Bulalacao Hardware & Builders Supply'], [
            'contact_person' => 'Vicente Alcantara',
            'email' => 'sales@bulalacaobuilders.ph',
            'phone' => '+63 (43) 288-4100',
            'address' => 'National Highway, Poblacion, Bulalacao, Oriental Mindoro',
        ]);

        $sup2 = Supplier::firstOrCreate(['name' => 'Southern Mindoro Steel & Aggregates Corp.'], [
            'contact_person' => 'Elena Ramirez',
            'email' => 'orders@mindorosteel.com.ph',
            'phone' => '+63 (43) 283-9200',
            'address' => 'Roxas Port Access Road, Roxas, Oriental Mindoro',
        ]);

        $sup3 = Supplier::firstOrCreate(['name' => 'Island Pacific Cement Industries'], [
            'contact_person' => 'Engr. Daniel Tan',
            'email' => 'logistics@pacificcement.ph',
            'phone' => '+63 (2) 8876-5432',
            'address' => 'Calapan Industrial Estate, Calapan City, Oriental Mindoro',
        ]);

        // 7. Materials Dataset
        $materialsData = [
            [
                'name' => 'Portland Cement Type 1 (40kg)',
                'category_id' => $catCement->id,
                'supplier_id' => $sup3->id,
                'unit' => 'Bags',
                'description' => 'General purpose hydraulic cement for road pavement and structural foundations',
                'minimum_stock_level' => 100,
                'current_stock' => 380,
                'location_id' => $loc1->id,
                'status' => 'available',
                'qr_code' => 'MAT-BUL-CEM-001',
                'batch' => 'BATCH-2026-08A',
            ],
            [
                'name' => '16mm x 6m Grade 60 Deformed Rebar',
                'category_id' => $catSteel->id,
                'supplier_id' => $sup2->id,
                'unit' => 'Pcs',
                'description' => 'High-tensile reinforcement steel for bridge girders and retaining walls',
                'minimum_stock_level' => 80,
                'current_stock' => 240,
                'location_id' => $loc2->id,
                'status' => 'available',
                'qr_code' => 'MAT-BUL-STL-016',
                'batch' => 'BATCH-2026-08B',
            ],
            [
                'name' => '12mm x 6m Grade 40 Deformed Rebar',
                'category_id' => $catSteel->id,
                'supplier_id' => $sup2->id,
                'unit' => 'Pcs',
                'description' => 'Standard structural steel reinforcement for drainage boxes and curb barriers',
                'minimum_stock_level' => 120,
                'current_stock' => 95, // Low stock!
                'location_id' => $loc2->id,
                'status' => 'low_stock',
                'qr_code' => 'MAT-BUL-STL-012',
                'batch' => 'BATCH-2026-08C',
            ],
            [
                'name' => 'G-1 Crushed Basalt Aggregates (3/4")',
                'category_id' => $catAggregates->id,
                'supplier_id' => $sup2->id,
                'unit' => 'Cu.m',
                'description' => 'Graded volcanic crushed stone for ready-mix concrete batching',
                'minimum_stock_level' => 40,
                'current_stock' => 110,
                'location_id' => $loc2->id,
                'status' => 'available',
                'qr_code' => 'MAT-BUL-AGG-001',
                'batch' => 'BATCH-2026-08D',
            ],
            [
                'name' => 'Washed Fine River Sand',
                'category_id' => $catAggregates->id,
                'supplier_id' => $sup1->id,
                'unit' => 'Cu.m',
                'description' => 'Screened river sand free of clay and organic matter for concrete mortar',
                'minimum_stock_level' => 30,
                'current_stock' => 18, // Low stock!
                'location_id' => $loc2->id,
                'status' => 'low_stock',
                'qr_code' => 'MAT-BUL-SND-001',
                'batch' => 'BATCH-2026-08E',
            ],
            [
                'name' => '2" x 4" x 12\' Good Coco Lumber',
                'category_id' => $catLumber->id,
                'supplier_id' => $sup1->id,
                'unit' => 'Bd.ft',
                'description' => 'Seasoned lumber for roadway formwork bracing and scaffolding struts',
                'minimum_stock_level' => 150,
                'current_stock' => 420,
                'location_id' => $loc1->id,
                'status' => 'available',
                'qr_code' => 'MAT-BUL-LMB-204',
                'batch' => 'BATCH-2026-08F',
            ],
            [
                'name' => '1/2" x 4\' x 8\' Phenolic Marine Plywood',
                'category_id' => $catLumber->id,
                'supplier_id' => $sup1->id,
                'unit' => 'Sheets',
                'description' => 'Heavy-duty water-resistant form boards for smooth concrete wall finishes',
                'minimum_stock_level' => 50,
                'current_stock' => 85,
                'location_id' => $loc1->id,
                'status' => 'available',
                'qr_code' => 'MAT-BUL-PLY-002',
                'batch' => 'BATCH-2026-08G',
            ],
            [
                'name' => '6" Series 1000 PVC Drainage Pipe (6m)',
                'category_id' => $catPlumbing->id,
                'supplier_id' => $sup1->id,
                'unit' => 'Pcs',
                'description' => 'Heavy underground stormwater drainage conduit',
                'minimum_stock_level' => 25,
                'current_stock' => 45,
                'location_id' => $loc4->id,
                'status' => 'available',
                'qr_code' => 'MAT-BUL-PVC-006',
                'batch' => 'BATCH-2026-08H',
            ],
            [
                'name' => 'Industrial Heavy-Duty Safety Helmets (ANSI Z89.1)',
                'category_id' => $catPPE->id,
                'supplier_id' => $sup1->id,
                'unit' => 'Pcs',
                'description' => 'Site personnel impact-resistant safety gear with chin straps',
                'minimum_stock_level' => 20,
                'current_stock' => 8, // Low stock!
                'location_id' => $loc1->id,
                'status' => 'low_stock',
                'qr_code' => 'MAT-BUL-PPE-001',
                'batch' => 'BATCH-2026-08I',
            ],
            [
                'name' => 'Hydraulic Excavator Oil (55 Gal Drum)',
                'category_id' => $catCement->id,
                'supplier_id' => $sup2->id,
                'unit' => 'Drums',
                'description' => 'ISO 68 anti-wear hydraulic fluid for heavy backhoes and compactors',
                'minimum_stock_level' => 4,
                'current_stock' => 6,
                'location_id' => $loc4->id,
                'status' => 'available',
                'qr_code' => 'MAT-BUL-OIL-068',
                'batch' => 'BATCH-2026-08J',
            ],
        ];

        foreach ($materialsData as $data) {
            $qrCodeText = $data['qr_code'];
            $batch = $data['batch'];
            unset($data['qr_code'], $data['batch']);

            $mat = Material::firstOrCreate(['name' => $data['name']], $data);

            $qr = QrCode::firstOrCreate(['code' => $qrCodeText], [
                'material_id' => $mat->id,
                'batch_number' => $batch,
                'status' => 'active',
                'generated_by' => $officer->id,
            ]);

            // Seed inventory mapping
            Inventory::firstOrCreate([
                'material_id' => $mat->id,
                'location_id' => $mat->location_id,
            ], [
                'quantity' => $mat->current_stock,
                'last_updated_by' => $officer->id,
            ]);
        }

        // 8. Seed Sample Realistic Transactions
        $cement = Material::where('name', 'like', '%Portland Cement%')->first();
        $rebar = Material::where('name', 'like', '%16mm%')->first();
        $sand = Material::where('name', 'like', '%River Sand%')->first();

        if ($cement) {
            $cementQr = $cement->qrCodes()->first();
            Transaction::firstOrCreate(['reference_number' => 'REC-20260810-1001'], [
                'material_id' => $cement->id,
                'qr_code_id' => $cementQr ? $cementQr->id : null,
                'type' => 'received',
                'quantity' => 500,
                'from_location_id' => null,
                'to_location_id' => $loc1->id,
                'performed_by' => $officer->id,
                'remarks' => 'Direct delivery from Island Pacific Cement via Batangas RORO to Bulalacao Depot',
            ]);

            Transaction::firstOrCreate(['reference_number' => 'TRF-20260814-1002'], [
                'material_id' => $cement->id,
                'qr_code_id' => $cementQr ? $cementQr->id : null,
                'type' => 'transferred',
                'quantity' => 120,
                'from_location_id' => $loc1->id,
                'to_location_id' => $loc3->id,
                'performed_by' => $siteUser->id,
                'remarks' => 'Dispatched for Poblacion Section 2 concrete pouring schedule',
            ]);
        }

        if ($rebar) {
            $rebarQr = $rebar->qrCodes()->first();
            Transaction::firstOrCreate(['reference_number' => 'REC-20260812-2001'], [
                'material_id' => $rebar->id,
                'qr_code_id' => $rebarQr ? $rebarQr->id : null,
                'type' => 'received',
                'quantity' => 300,
                'from_location_id' => null,
                'to_location_id' => $loc2->id,
                'performed_by' => $officer->id,
                'remarks' => 'Delivery from Southern Mindoro Steel Corporation - 16mm Rebars',
            ]);

            Transaction::firstOrCreate(['reference_number' => 'ISS-20260815-2002'], [
                'material_id' => $rebar->id,
                'qr_code_id' => $rebarQr ? $rebarQr->id : null,
                'type' => 'issued',
                'quantity' => 60,
                'from_location_id' => $loc2->id,
                'to_location_id' => $loc3->id,
                'performed_by' => $siteUser->id,
                'remarks' => 'Issued to Steelfixing Crew Team B for coastal retaining wall cage',
            ]);
        }

        if ($sand) {
            $sandQr = $sand->qrCodes()->first();
            Transaction::firstOrCreate(['reference_number' => 'DAM-20260813-3001'], [
                'material_id' => $sand->id,
                'qr_code_id' => $sandQr ? $sandQr->id : null,
                'type' => 'damaged',
                'quantity' => 4,
                'from_location_id' => $loc2->id,
                'to_location_id' => $loc2->id,
                'performed_by' => $siteUser->id,
                'remarks' => 'Heavy monsoon rain runoff washed out 4 cu.m from un-tarpaulined staging sector',
            ]);
        }

        // 9. Initial Activity Logs
        ActivityLog::firstOrCreate(['action' => 'system_init', 'module' => 'System'], [
            'user_id' => $admin->id,
            'description' => 'Construction logistics system initialized for Bulalacao, Oriental Mindoro',
            'ip_address' => '127.0.0.1',
            'properties' => ['version' => '2.0.0', 'environment' => 'Production Ready'],
        ]);

        ActivityLog::firstOrCreate(['action' => 'batch_qr_generated', 'module' => 'QR Code'], [
            'user_id' => $officer->id,
            'description' => 'Generated 10 master batch QR codes for Bulalacao Municipal Infrastructure Project',
            'ip_address' => '127.0.0.1',
            'properties' => ['batch_count' => 10],
        ]);
    }
}

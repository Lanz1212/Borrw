<?php

namespace Database\Seeders;

use App\Models\Borrower;
use App\Models\DamagedItem;
use App\Models\Inventory;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ────────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            ['name' => 'Administrator', 'email' => 'admin@sparepart.local',
             'password' => Hash::make('admin123'), 'role' => 'admin', 'active' => true]
        );

        User::firstOrCreate(
            ['username' => 'operator'],
            ['name' => 'Budi Operator', 'email' => 'operator@sparepart.local',
             'password' => Hash::make('operator123'), 'role' => 'user', 'active' => true]
        );

        // ── Settings ─────────────────────────────────────────────────────────
        foreach ([
            'app_name'     => 'Sparepart MS',
            'company_name' => 'PT. Maju Bersama',
            'categories'   => 'Mekanik,Elektrik,Hidrolik,Pneumatik,Umum',
        ] as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        if (Inventory::count() > 0) return;

        // ── Inventory ────────────────────────────────────────────────────────
        $items = Inventory::insert([
            ['code'=>'BRG-001','name'=>'Bearing 6205 ZZ','category'=>'Mekanik','type'=>'pinjam','total_qty'=>10,'available_qty'=>8,'min_stock'=>3,'condition'=>'baik','notes'=>'SKF brand','created_at'=>now(),'updated_at'=>now()],
            ['code'=>'BLT-001','name'=>'V-Belt A42','category'=>'Mekanik','type'=>'consumable','total_qty'=>20,'available_qty'=>15,'min_stock'=>5,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'KNC-001','name'=>'Kunci Ring 17mm','category'=>'Mekanik','type'=>'pinjam','total_qty'=>5,'available_qty'=>4,'min_stock'=>2,'condition'=>'baik','notes'=>'Set Tekiro','created_at'=>now(),'updated_at'=>now()],
            ['code'=>'OLI-001','name'=>'Oli Mesin SAE 40 (L)','category'=>'Mekanik','type'=>'consumable','total_qty'=>50,'available_qty'=>35,'min_stock'=>10,'condition'=>'baik','notes'=>'Shell Rimula','created_at'=>now(),'updated_at'=>now()],
            ['code'=>'RLY-001','name'=>'Relay 24VDC Omron','category'=>'Elektrik','type'=>'pinjam','total_qty'=>8,'available_qty'=>6,'min_stock'=>2,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'KBL-001','name'=>'Kabel NYM 2.5mm (m)','category'=>'Elektrik','type'=>'consumable','total_qty'=>100,'available_qty'=>70,'min_stock'=>20,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'MTR-001','name'=>'Multimeter Digital','category'=>'Elektrik','type'=>'pinjam','total_qty'=>3,'available_qty'=>2,'min_stock'=>1,'condition'=>'baik','notes'=>'Sanwa CD800a','created_at'=>now(),'updated_at'=>now()],
            ['code'=>'SLG-001','name'=>'Selang Hidrolik 1/2"','category'=>'Hidrolik','type'=>'pinjam','total_qty'=>6,'available_qty'=>5,'min_stock'=>2,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'SKT-001','name'=>'Seal Kit Silinder Hid.','category'=>'Hidrolik','type'=>'consumable','total_qty'=>15,'available_qty'=>10,'min_stock'=>3,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'FTG-001','name'=>'Fitting Selang 1/4"','category'=>'Pneumatik','type'=>'pinjam','total_qty'=>10,'available_qty'=>9,'min_stock'=>3,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'KNI-001','name'=>'Kunci Inggris 8"','category'=>'Umum','type'=>'pinjam','total_qty'=>4,'available_qty'=>3,'min_stock'=>1,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'AMP-001','name'=>'Ampelas P120 (lembar)','category'=>'Umum','type'=>'consumable','total_qty'=>50,'available_qty'=>40,'min_stock'=>10,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['code'=>'GRS-001','name'=>'Grease Gemuk (kg)','category'=>'Mekanik','type'=>'consumable','total_qty'=>10,'available_qty'=>7,'min_stock'=>2,'condition'=>'baik','notes'=>'SKF LGMT2','created_at'=>now(),'updated_at'=>now()],
            ['code'=>'TNG-001','name'=>'Tang Kombinasi 8"','category'=>'Umum','type'=>'pinjam','total_qty'=>6,'available_qty'=>5,'min_stock'=>2,'condition'=>'baik','notes'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // ── Borrowers ────────────────────────────────────────────────────────
        $borrowers = [];
        foreach ([
            ['Ahmad Fauzi',      '08123456789',  'Forklift',      'Operator Forklift Area A'],
            ['Budi Santoso',     '08234567890',  'Maintenance',   ''],
            ['Citra Dewi',       '08345678901',  'Quality Control',''],
            ['Doni Prasetyo',    '08456789012',  'Produksi',      'Shift Pagi'],
            ['Eko Supriyanto',   '08567890123',  'Warehouse',     ''],
            ['Fitri Handayani',  '08678901234',  'Elektrik',      'Teknisi Listrik'],
            ['Gunawan Hidayat',  '08789012345',  'Mekanik',       'Teknisi Mekanik Senior'],
            ['Hendra Kusuma',    '08890123456',  'Operator',      'Operator Mesin B'],
        ] as [$name, $contact, $dept, $notes]) {
            $borrowers[] = Borrower::create(compact('name', 'contact') + ['department' => $dept, 'notes' => $notes]);
        }

        // ── Transactions + Details ────────────────────────────────────────────
        $inv = Inventory::all()->keyBy('code');

        // TRX 1: Aktif – Budi meminjam Bearing + Kunci Ring
        $trx1 = Transaction::create([
            'transaction_code' => 'TRX-20250501-0001',
            'borrower_id'      => $borrowers[1]->id,
            'borrower_name'    => $borrowers[1]->name,
            'loan_date'        => now()->subDays(5),
            'status'           => 'aktif',
            'notes'            => 'Untuk penggantian bearing mesin press',
            'created_by'       => $admin->id,
            'created_by_name'  => $admin->name,
        ]);
        TransactionDetail::create(['transaction_id'=>$trx1->id,'inventory_id'=>$inv['BRG-001']->id,'item_name'=>$inv['BRG-001']->name,'item_code'=>'BRG-001','item_type'=>'pinjam','qty'=>2,'status'=>'dipinjam','qty_returned'=>0]);
        TransactionDetail::create(['transaction_id'=>$trx1->id,'inventory_id'=>$inv['KNC-001']->id,'item_name'=>$inv['KNC-001']->name,'item_code'=>'KNC-001','item_type'=>'pinjam','qty'=>1,'status'=>'dipinjam','qty_returned'=>0]);
        $inv['BRG-001']->decrement('available_qty', 2);
        $inv['KNC-001']->decrement('available_qty', 1);

        // TRX 2: Selesai – Ahmad mengambil V-Belt + Oli
        $trx2 = Transaction::create([
            'transaction_code' => 'TRX-20250428-0002',
            'borrower_id'      => $borrowers[0]->id,
            'borrower_name'    => $borrowers[0]->name,
            'loan_date'        => now()->subDays(10),
            'return_date'      => now()->subDays(8),
            'status'           => 'selesai',
            'notes'            => 'Penggantian rutin',
            'created_by'       => $admin->id,
            'created_by_name'  => $admin->name,
        ]);
        TransactionDetail::create(['transaction_id'=>$trx2->id,'inventory_id'=>$inv['BLT-001']->id,'item_name'=>$inv['BLT-001']->name,'item_code'=>'BLT-001','item_type'=>'consumable','qty'=>2,'status'=>'dipakai','qty_returned'=>0]);
        TransactionDetail::create(['transaction_id'=>$trx2->id,'inventory_id'=>$inv['OLI-001']->id,'item_name'=>$inv['OLI-001']->name,'item_code'=>'OLI-001','item_type'=>'consumable','qty'=>5,'status'=>'dipakai','qty_returned'=>0]);
        $inv['BLT-001']->decrement('available_qty', 2);
        $inv['OLI-001']->decrement('available_qty', 5);

        // TRX 3: Aktif – Gunawan meminjam Multimeter + Relay
        $trx3 = Transaction::create([
            'transaction_code' => 'TRX-20250503-0003',
            'borrower_id'      => $borrowers[6]->id,
            'borrower_name'    => $borrowers[6]->name,
            'loan_date'        => now()->subDays(2),
            'status'           => 'aktif',
            'notes'            => 'Pengerjaan panel kontrol',
            'created_by'       => $admin->id,
            'created_by_name'  => $admin->name,
        ]);
        TransactionDetail::create(['transaction_id'=>$trx3->id,'inventory_id'=>$inv['MTR-001']->id,'item_name'=>$inv['MTR-001']->name,'item_code'=>'MTR-001','item_type'=>'pinjam','qty'=>1,'status'=>'dipinjam','qty_returned'=>0]);
        TransactionDetail::create(['transaction_id'=>$trx3->id,'inventory_id'=>$inv['RLY-001']->id,'item_name'=>$inv['RLY-001']->name,'item_code'=>'RLY-001','item_type'=>'pinjam','qty'=>2,'status'=>'dipinjam','qty_returned'=>0]);
        $inv['MTR-001']->decrement('available_qty', 1);
        $inv['RLY-001']->decrement('available_qty', 2);

        // TRX 4: Selesai – Fitri mengambil Kabel + Ampelas
        $trx4 = Transaction::create([
            'transaction_code' => 'TRX-20250415-0004',
            'borrower_id'      => $borrowers[5]->id,
            'borrower_name'    => $borrowers[5]->name,
            'loan_date'        => now()->subDays(18),
            'return_date'      => now()->subDays(16),
            'status'           => 'selesai',
            'notes'            => '',
            'created_by'       => $admin->id,
            'created_by_name'  => $admin->name,
        ]);
        TransactionDetail::create(['transaction_id'=>$trx4->id,'inventory_id'=>$inv['KBL-001']->id,'item_name'=>$inv['KBL-001']->name,'item_code'=>'KBL-001','item_type'=>'consumable','qty'=>10,'status'=>'dipakai','qty_returned'=>0]);
        TransactionDetail::create(['transaction_id'=>$trx4->id,'inventory_id'=>$inv['AMP-001']->id,'item_name'=>$inv['AMP-001']->name,'item_code'=>'AMP-001','item_type'=>'consumable','qty'=>5,'status'=>'dipakai','qty_returned'=>0]);
        $inv['KBL-001']->decrement('available_qty', 10);
        $inv['AMP-001']->decrement('available_qty', 5);

        // TRX 5: Aktif – Doni meminjam Selang + Fitting
        $trx5 = Transaction::create([
            'transaction_code' => 'TRX-20250504-0005',
            'borrower_id'      => $borrowers[3]->id,
            'borrower_name'    => $borrowers[3]->name,
            'loan_date'        => now()->subDays(1),
            'status'           => 'aktif',
            'notes'            => 'Perbaikan sistem hidrolik mesin A',
            'created_by'       => $admin->id,
            'created_by_name'  => $admin->name,
        ]);
        TransactionDetail::create(['transaction_id'=>$trx5->id,'inventory_id'=>$inv['SLG-001']->id,'item_name'=>$inv['SLG-001']->name,'item_code'=>'SLG-001','item_type'=>'pinjam','qty'=>1,'status'=>'dipinjam','qty_returned'=>0]);
        TransactionDetail::create(['transaction_id'=>$trx5->id,'inventory_id'=>$inv['FTG-001']->id,'item_name'=>$inv['FTG-001']->name,'item_code'=>'FTG-001','item_type'=>'pinjam','qty'=>2,'status'=>'dipinjam','qty_returned'=>0]);
        $inv['SLG-001']->decrement('available_qty', 1);
        $inv['FTG-001']->decrement('available_qty', 2);

        // ── Sample Damaged Item ────────────────────────────────────────────────
        DamagedItem::create([
            'inventory_id'     => $inv['BRG-001']->id,
            'item_name'        => $inv['BRG-001']->name,
            'qty'              => 1,
            'description'      => 'Bearing pecah akibat kelebihan beban pada mesin press',
            'reported_by'      => $admin->id,
            'reported_by_name' => $admin->name,
        ]);
    }
}

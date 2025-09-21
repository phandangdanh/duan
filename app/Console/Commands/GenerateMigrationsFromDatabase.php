<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class GenerateMigrationsFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:generate-from-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migrations from existing database tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating migrations from existing database...');
        
        // Lấy danh sách tất cả bảng
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableColumn = 'Tables_in_' . $databaseName;
        
        $migrationOrder = [
            'users',
            'personal_access_tokens', 
            'password_reset_tokens',
            'roles',
            'permissions',
            'role_permissions',
            'role_user',
            'audit_logs',
            'provinces',
            'districts', 
            'wards',
            'danhmuc',
            'sanpham',
            'chitietsanpham',
            'mausac',
            'size',
            'sanpham_hinhanh',
            'sanpham_mausac_images',
            'donhang',
            'chitietdonhang',
            'donhang_voucher',
            'voucher',
            'binhluan',
            'danhgia'
        ];
        
        foreach ($migrationOrder as $tableName) {
            if (Schema::hasTable($tableName)) {
                $this->generateMigrationForTable($tableName);
            }
        }
        
        $this->info('Migration generation completed!');
    }
    
    private function generateMigrationForTable($tableName)
    {
        $this->info("Generating migration for table: {$tableName}");
        
        // Lấy cấu trúc bảng
        $columns = DB::select("DESCRIBE {$tableName}");
        $indexes = DB::select("SHOW INDEX FROM {$tableName}");
        
        $migrationContent = $this->buildMigrationContent($tableName, $columns, $indexes);
        
        // Tạo file migration
        $timestamp = date('Y_m_d_His');
        $className = 'Create' . \Illuminate\Support\Str::studly($tableName) . 'Table';
        $fileName = "{$timestamp}_create_{$tableName}_table.php";
        $filePath = database_path("migrations/{$fileName}");
        
        File::put($filePath, $migrationContent);
        
        $this->line("Created: {$fileName}");
    }
    
    private function buildMigrationContent($tableName, $columns, $indexes)
    {
        $className = 'Create' . \Illuminate\Support\Str::studly($tableName) . 'Table';
        
        $content = "<?php\n\n";
        $content .= "use Illuminate\Database\Migrations\Migration;\n";
        $content .= "use Illuminate\Database\Schema\Blueprint;\n";
        $content .= "use Illuminate\Support\Facades\Schema;\n\n";
        $content .= "return new class extends Migration\n";
        $content .= "{\n";
        $content .= "    public function up(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
        
        foreach ($columns as $column) {
            $line = $this->buildColumnDefinition($column);
            $content .= "            {$line}\n";
        }
        
        $content .= "        });\n";
        $content .= "    }\n\n";
        $content .= "    public function down(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::dropIfExists('{$tableName}');\n";
        $content .= "    }\n";
        $content .= "};\n";
        
        return $content;
    }
    
    private function buildColumnDefinition($column)
    {
        $name = $column->Field;
        $type = $column->Type;
        $null = $column->Null === 'YES' ? '->nullable()' : '';
        $key = $column->Key;
        $default = $column->Default;
        $extra = $column->Extra;
        
        $definition = "\$table->";
        
        // Xử lý kiểu dữ liệu
        if (strpos($type, 'bigint') !== false) {
            if ($key === 'PRI' && $extra === 'auto_increment') {
                $definition .= 'id()';
            } else {
                $definition .= "bigInteger('{$name}')";
            }
        } elseif (strpos($type, 'int') !== false) {
            $definition .= "integer('{$name}')";
        } elseif (strpos($type, 'varchar') !== false) {
            $length = preg_replace('/[^0-9]/', '', $type);
            $definition .= "string('{$name}'" . ($length ? ", {$length}" : '') . ")";
        } elseif (strpos($type, 'text') !== false) {
            $definition .= "text('{$name}')";
        } elseif (strpos($type, 'longtext') !== false) {
            $definition .= "longText('{$name}')";
        } elseif (strpos($type, 'timestamp') !== false) {
            $definition .= "timestamp('{$name}')";
        } elseif (strpos($type, 'datetime') !== false) {
            $definition .= "dateTime('{$name}')";
        } elseif (strpos($type, 'decimal') !== false) {
            $definition .= "decimal('{$name}')";
        } elseif (strpos($type, 'float') !== false) {
            $definition .= "float('{$name}')";
        } elseif (strpos($type, 'boolean') !== false || strpos($type, 'tinyint(1)') !== false) {
            $definition .= "boolean('{$name}')";
        } else {
            $definition .= "string('{$name}')";
        }
        
        if ($key === 'UNI') {
            $definition .= '->unique()';
        }
        
        if ($null) {
            $definition .= $null;
        }
        
        if ($default !== null) {
            if ($default === 'CURRENT_TIMESTAMP') {
                $definition .= '->useCurrent()';
            } else {
                $definition .= "->default('{$default}')";
            }
        }
        
        $definition .= ';';
        
        return $definition;
    }
}

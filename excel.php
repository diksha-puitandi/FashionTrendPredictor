require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('data.xlsx');
$sheetData = $spreadsheet->getActiveSheet()->toArray();

     class ExportToExcel {
        var $xlsData = ""; 
        var $fileName = ""; 
        var $countRow = 0; 
        var $countCol = 0; 
        var $totalCol = 3;//общее число  колонок в Excel
                //конструктор класса
        function __construct (){
                $this->xlsData = pack( "ssssss", 0x809, 0x08, 0x00,0x10, 0x0, 0x0 );
        }
                // Если число
        function RecNumber( $row, $col, $value ){
                $this->xlsData .= pack( "sssss", 0x0203, 14, $row, $col, 0x00 );
                $this->xlsData .= pack( "d", $value );
                return;
        }
                //Если текст
        function RecText( $row, $col, $value ){
                $len = strlen( $value );
                $this->xlsData .= pack( "s*", 0x0204, 8 + $len, $row, $col, 0x00, $len);
                $this->xlsData .= $value;
                return;
        }
                // Вставляем число
        function InsertNumber( $value ){
                if ( $this->countCol == $this->totalCol ) {
                        $this->countCol = 0;
                        $this->countRow++;
                }
                $this->RecNumber( $this->countRow, $this->countCol, $value );
                $this->countCol++;
                return;
        }
                // Вставляем текст
        function InsertText( $value ){
                if ( $this->countCol == $this->totalCol ) {
                        $this->countCol = 0;
                        $this->countRow++;
        }
                $this->RecText( $this->countRow, $this->countCol, $value );
                $this->countCol++;
                return;
        }
                // Переход на новую строку
        function GoNewLine(){
                $this->countCol = 0;
                $this->countRow++;
                return;
                }
                //Конец данных
        function EndData(){
                $this->xlsData .= pack( "ss", 0x0A, 0x00 );
                return;
        }
                // Сохраняем файл
        function SaveFile( $fileName ){
                $this->fileName = $fileName;
                $this->SendFile();
        }
                // Отправляем файл
        function SendFile(){
                $this->EndData();
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-store, no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/x-msexcel" );
                header ( "Content-Disposition: attachment; fileName=$this->fileName.xls" );
                print $this->xlsData;
         }
        } 

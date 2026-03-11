<?php
include "../../config.php";

$filename = "Inventory_Report_" . date('Y-m-d') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

echo "<?xml version=\"1.0\"?>\n";
echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
  </Style>

  <Style ss:ID="Header">
   <Font ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#C00000" ss:Pattern="Solid"/> 
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="CellCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="Currency">
   <NumberFormat ss:Format="&quot;₱&quot;#,##0.00"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="BoldTotal">
   <Font ss:Bold="1"/>
   <Interior ss:Color="#F2F2F2" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <NumberFormat ss:Format="&quot;₱&quot;#,##0.00"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
 </Styles>

 <Worksheet ss:Name="Inventory">
  <Table>
   <Column ss:Width="150"/> 
   <Column ss:Width="200"/> 
   <Column ss:Width="100"/> 
   <Column ss:Width="80"/> 
   <Column ss:Width="100"/> 
   <Column ss:Width="120"/> 

   <Row ss:Height="22">
    <Cell ss:StyleID="Header"><Data ss:Type="String">ITEM NAME</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">DESCRIPTION</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">CABINET</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">QTY</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">UNIT PRICE</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">TOTAL VALUE</Data></Cell>
   </Row>

   <?php
   // Inalis ang 'id' sa SQL Query
   $query = "SELECT item, description, cabinet, quantity, price FROM inventory ORDER BY id DESC";
   $result = $conn->query($query);
   $grandTotal = 0;

   if ($result && $result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $total_value = $row['quantity'] * $row['price'];
           $grandTotal += $total_value;
           
           echo "<Row>";
           // Inalis ang ID cell row dito
           echo "<Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"String\">" . htmlspecialchars($row['item']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"String\">" . htmlspecialchars($row['description']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"String\">" . htmlspecialchars($row['cabinet']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"CellCenter\"><Data ss:Type=\"Number\">{$row['quantity']}</Data></Cell>";
           echo "<Cell ss:StyleID=\"Currency\"><Data ss:Type=\"Number\">{$row['price']}</Data></Cell>";
           echo "<Cell ss:StyleID=\"Currency\"><Data ss:Type=\"Number\">$total_value</Data></Cell>";
           echo "</Row>";
       }
   }
   ?>

   <Row>
    <Cell ss:Index="5" ss:StyleID="Header"><Data ss:Type="String">GRAND TOTAL</Data></Cell>
    <Cell ss:StyleID="BoldTotal"><Data ss:Type="Number"><?= $grandTotal ?></Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>
<?php exit(); ?>
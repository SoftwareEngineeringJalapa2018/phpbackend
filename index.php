<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'includes/conexion.php';
require 'includes/funciones.php';

$app = new \Slim\App;

//Desde aca comienzan los metodos del api rest

//get('indica la direccion que utlizaremos para el metodo') en esta ocasion 1
$app->get('/stock', function (Request $request, Response $response) {
    
    //Realizamos la consulta
    $consulta = "SELECT TOP 10 P.ProductID, P.Name as ProductName, SUM(PIV.Quantity) AS Stock, SUM(SOD.OrderQty) AS QuantitySold,
                     CONVERT(date,MAX(SOH.OrderDate)) AS LastSoldDate, (SELECT TOP 1 pe.LastName + ' ' + pe.FirstName
                     FROM Sales.SalesOrderDetail sd
                     inner join Sales.SalesOrderHeader soh on sd.SalesOrderID =soh.SalesOrderID
                     inner join Sales.Customer c on soh.CustomerID=c.CustomerID
                     inner join Person.Person pe on c.PersonID=pe.BusinessEntityID
                     where ProductID = P.ProductID
                     GROUP BY pe.LastName + ' ' + pe.FirstName
                     ORDER BY COUNT(1) DESC) as BestCustomer
                     FROM Production.ProductInventory PIV
                     INNER JOIN Production.Product P ON PIV.ProductID=P.ProductID
                     INNER JOIN Sales.SalesOrderDetail SOD ON P.ProductID = SOD.ProductID
                     INNER JOIN Sales.SalesOrderHeader SOH ON SOD.SalesOrderID = SOH.SalesOrderID
                     GROUP By  P.ProductID, P.Name
                     ORDER By  Stock asc,QuantitySold desc";

    try{
        //Conectamos con la base de datos
        $conectar = conectar();

        //Ejecutamos la consulta anterior
        $ejecutar = ejecutar($conectar,$consulta);

        //Almacenamos la consulta en una variable de arreglo php
        $datos = consulta_array($ejecutar);
        
        //var_dump($datos);

        //Mostramos el resultado de la consulta en formato JSON
        echo json_encode($datos);

        //Cerramos la conexion a la base de datos
        sqlsrv_close($conectar);

    }catch(Exception $e){

        $datos = "error";

    }

   
});

$app->get('/2', function (Request $request, Response $response) {


    $consulta = "SELECT 

    PCS.ProductSubcategoryID, PCS.Name,
    
    SUM(WO.StockedQty) AS WorkOrderQty, 
    
    SUM(WR.ActualCost* WO.OrderQty) AS WorkOrderCost,
    
    SUM(CAST(POD.OrderQty AS BIGINT)) PurchaseOrderQty,
    
    SUM(POD.UnitPrice*POD.OrderQty) AS PurchaseOrderCost 
    
    FROM Purchasing.PurchaseOrderDetail POD
    
    INNER JOIN Production.Product P ON POD.ProductID=POD.ProductID
    
    INNER JOIN Production.ProductSubcategory PCS ON P.ProductSubcategoryID=PCS.ProductSubcategoryID
    
    INNER JOIN Production.WorkOrder WO ON P.ProductID=WO.ProductID
    
    INNER JOIN Production.WorkOrderRouting WR ON WR.WorkOrderID = WO.WorkOrderID
    
    where year(WO.StartDate)=2014 
    
    GROUP BY PCS.ProductSubcategoryID, PCS.Name";

    try{
        $conectar = conectar();

        $ejecutar = ejecutar($conectar,$consulta);

        $datos = consulta_array($ejecutar);
        
        //var_dump($datos);

        echo json_encode($datos);

    }catch(Exception $e){

        $datos = "error";

    }

   
});


$app->run();

?>
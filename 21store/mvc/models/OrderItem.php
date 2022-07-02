
<?php require_once ROOT . DS . 'services' . DS . "ProductService.php";

class OrderItem {
    private $id;                  
    private $billId;            
    private $productId;              
    private $quantity;           
    private $createdAt;              

    public function __construct($order) {
        $this->id = $order->id;
        $this->billId = $order->billId;
        $this->createdAt = $order->createdAt;
        $this->productId = $order->productId;
        $this->quantity = $order->quantity;
    }

    public function getId() {
        return $this->id;
    }

    public function getBillId() {
        return $this->billId;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }
    public function getProduct() {
        $productService = new ProductService();
        return $productService->getProduct($this->id);
    }
}
?>
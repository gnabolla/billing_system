<?php

require_once __DIR__ . '/../vendor/fpdf/fpdf.php';

class StatementGenerator extends FPDF
{
    private $company;
    private $statement;
    private $subscriber;
    private $items;
    private $payments;
    
    public function __construct($company, $statement, $subscriber, $items, $payments = [])
    {
        parent::__construct();
        $this->company = $company;
        $this->statement = $statement;
        $this->subscriber = $subscriber;
        $this->items = $items;
        $this->payments = $payments;
    }
    
    public function generate()
    {
        $this->AddPage();
        
        // Add header with company information
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $this->company['company_name'], 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->company['address'] ?? '', 0, 1, 'C');
        $this->Cell(0, 6, 'Phone: ' . ($this->company['contact_phone'] ?? ''), 0, 1, 'C');
        $this->Cell(0, 6, 'Email: ' . ($this->company['contact_email'] ?? ''), 0, 1, 'C');
        
        $this->Ln(10);
        
        // Statement title
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'STATEMENT OF ACCOUNT', 0, 1, 'C');
        
        $this->Ln(5);
        
        // Statement details
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'Statement Number:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, $this->statement['statement_no'], 0);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'Date Issued:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, date('F d, Y', strtotime($this->statement['created_at'])), 0, 1);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'Billing Period:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, date('M d, Y', strtotime($this->statement['bill_period_start'])) . ' to ' . 
                          date('M d, Y', strtotime($this->statement['bill_period_end'])), 0);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, 'Due Date:', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 8, date('F d, Y', strtotime($this->statement['due_date'])), 0, 1);
        
        $this->Ln(5);
        
        // Subscriber details
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Billed To:', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        if (!empty($this->subscriber['company_name'])) {
            $this->Cell(0, 6, $this->subscriber['company_name'], 0, 1);
        } else {
            $this->Cell(0, 6, $this->subscriber['first_name'] . ' ' . $this->subscriber['last_name'], 0, 1);
        }
        $this->Cell(0, 6, 'Account No: ' . $this->subscriber['account_no'], 0, 1);
        $this->Cell(0, 6, $this->subscriber['address'], 0, 1);
        if (!empty($this->subscriber['email'])) {
            $this->Cell(0, 6, 'Email: ' . $this->subscriber['email'], 0, 1);
        }
        if (!empty($this->subscriber['phone_number'])) {
            $this->Cell(0, 6, 'Phone: ' . $this->subscriber['phone_number'], 0, 1);
        }
        
        $this->Ln(5);
        
        // Create table header for statement items
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 10);
        
        $this->Cell(100, 8, 'Description', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Amount', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Tax', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Total', 1, 1, 'C', true);
        
        // Add items
        $this->SetFont('Arial', '', 10);
        
        foreach ($this->items as $item) {
            $this->Cell(100, 8, $item['description'], 1);
            $this->Cell(30, 8, 'P' . number_format($item['amount'], 2), 1, 0, 'R');
            $this->Cell(20, 8, 'P' . number_format($item['tax_amount'], 2), 1, 0, 'R');
            $this->Cell(30, 8, 'P' . number_format($item['total_amount'], 2), 1, 1, 'R');
        }
        
        // Add totals
        $this->SetFont('Arial', 'B', 10);
        
        $this->Cell(150, 8, 'Subtotal:', 1, 0, 'R');
        $this->Cell(30, 8, 'P' . number_format($this->statement['amount'], 2), 1, 1, 'R');
        
        if ($this->statement['tax_amount'] > 0) {
            $this->Cell(150, 8, 'Tax:', 1, 0, 'R');
            $this->Cell(30, 8, 'P' . number_format($this->statement['tax_amount'], 2), 1, 1, 'R');
        }
        
        if ($this->statement['discount_amount'] > 0) {
            $this->Cell(150, 8, 'Discount:', 1, 0, 'R');
            $this->Cell(30, 8, '-₱' . number_format($this->statement['discount_amount'], 2), 1, 1, 'R');
        }
        
        $this->Cell(150, 8, 'Total Amount:', 1, 0, 'R');
        $this->Cell(30, 8, 'P' . number_format($this->statement['total_amount'], 2), 1, 1, 'R');
        
        // Display payment information if available
        if ($this->statement['total_amount'] != $this->statement['unpaid_amount']) {
            $this->Cell(150, 8, 'Amount Paid:', 1, 0, 'R');
            $this->Cell(30, 8, 'P' . number_format($this->statement['total_amount'] - $this->statement['unpaid_amount'], 2), 1, 1, 'R');
            
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(150, 8, 'Balance Due:', 1, 0, 'R');
            $this->Cell(30, 8, 'P' . number_format($this->statement['unpaid_amount'], 2), 1, 1, 'R');
        }
        
        $this->Ln(5);
        
        // Payment status
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Payment Status: ', 0, 0);
        $this->SetFont('Arial', 'B', 12);
        
        if ($this->statement['status'] === 'Paid') {
            $this->SetTextColor(0, 128, 0); // Green
            $this->Cell(0, 10, 'PAID', 0, 1);
        } elseif ($this->statement['status'] === 'Partially Paid') {
            $this->SetTextColor(255, 128, 0); // Orange
            $this->Cell(0, 10, 'PARTIALLY PAID', 0, 1);
        } elseif ($this->statement['status'] === 'Overdue') {
            $this->SetTextColor(255, 0, 0); // Red
            $this->Cell(0, 10, 'OVERDUE', 0, 1);
        } else {
            $this->SetTextColor(128, 128, 128); // Gray
            $this->Cell(0, 10, 'UNPAID', 0, 1);
        }
        $this->SetTextColor(0, 0, 0); // Reset to black
        
        $this->Ln(5);
        
        // Payment history if available
        if (!empty($this->payments)) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Payment History', 0, 1);
            
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(40, 8, 'Receipt No.', 1, 0, 'C', true);
            $this->Cell(40, 8, 'Date', 1, 0, 'C', true);
            $this->Cell(40, 8, 'Method', 1, 0, 'C', true);
            $this->Cell(40, 8, 'Amount', 1, 1, 'C', true);
            
            $this->SetFont('Arial', '', 10);
            foreach ($this->payments as $payment) {
                $this->Cell(40, 8, $payment['or_no'], 1);
                $this->Cell(40, 8, date('M d, Y', strtotime($payment['payment_date'])), 1);
                $this->Cell(40, 8, $payment['payment_method'], 1);
                $this->Cell(40, 8, 'P' . number_format($payment['paid_amount'], 2), 1, 1, 'R');
            }
        }
        
        $this->Ln(10);
        
        // Notes
        if (!empty($this->statement['notes'])) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 8, 'Notes:', 0, 1);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(0, 6, $this->statement['notes'], 0, 'L');
            $this->Ln(5);
        }
        
        // Footer
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 6, 'Please include your Account Number and Statement Number when making a payment.', 0, 1, 'C');
        
        if ($this->statement['status'] !== 'Paid') {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 8, 'Please pay by ' . date('F d, Y', strtotime($this->statement['due_date'])) . ' to avoid late fees.', 0, 1, 'C');
        }
        
        // Return the PDF as a string
        return $this->Output('S');
    }
}
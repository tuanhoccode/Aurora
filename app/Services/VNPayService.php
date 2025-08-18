<?php
namespace App\Services;

class VNPayService
{
    protected $vnp_TmnCode = '3ANN0P8R'; 
    protected $vnp_HashSecret = '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y'; 
    protected $vnp_Url = 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction';

    public function refund($refund, $transactionId, $amount, $orderCode)
    {
        $data = [
            'vnp_RequestId' => time(),
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'refund',
            'vnp_TmnCode' => $this->vnp_TmnCode,
            'vnp_TransactionType' => '02', // Hoàn tiền toàn phần
            'vnp_Amount' => $amount * 100, // VNPay yêu cầu số tiền nhân 100
            'vnp_TxnRef' => $orderCode,
            'vnp_TransactionNo' => $transactionId,
            'vnp_TransactionDate' => $refund->created_at->format('YmdHis'),
            'vnp_CreateBy' => 'admin',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_IpAddr' => request()->ip(),
        ];

        ksort($data);
        $query = http_build_query($data);
        $hashdata = $query . $this->vnp_HashSecret;
        $data['vnp_SecureHash'] = hash('sha512', $hashdata);

        $response = $this->curlPost($this->vnp_Url, $data);

        \Log::info('VNPay refund response', ['refund_id' => $refund->id, 'response' => $response]);

        return $response['vnp_ResponseCode'] == '00';
    }

    private function curlPost($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
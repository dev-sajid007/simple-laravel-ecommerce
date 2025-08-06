<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CrmService
{
    private Client $client;
    private string $apiUrl;
    private string $apiKey;
    private string $apiSecret;
    private bool $enabled;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $this->apiUrl = config('crm.api_url');
        $this->apiKey = config('crm.api_key');
        $this->apiSecret = config('crm.api_secret');
        $this->enabled = config('crm.enabled', false);
    }

    /**
     * Track customer registration.
     */
    public function trackCustomerRegistration(Customer $customer): bool
    {
        if (!$this->enabled) {
            return true;
        }

        try {
            $data = [
                'event' => 'customer_registered',
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'phone' => $customer->phone,
                'accepts_marketing' => $customer->accepts_marketing,
                'registered_at' => $customer->created_at->toISOString(),
            ];

            return $this->sendEvent('customers/register', $data);
        } catch (\Exception $e) {
            Log::error('CRM: Failed to track customer registration', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Track order creation.
     */
    public function trackOrderCreation(Order $order): bool
    {
        if (!$this->enabled) {
            return true;
        }

        try {
            $order->load(['customer', 'orderItems.product']);

            $data = [
                'event' => 'order_created',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => $order->customer_id,
                'customer_email' => $order->customer?->email,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'line_items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->total,
                    ];
                })->toArray(),
                'billing_address' => $order->billing_address,
                'shipping_address' => $order->shipping_address,
                'created_at' => $order->created_at->toISOString(),
            ];

            return $this->sendEvent('orders/create', $data);
        } catch (\Exception $e) {
            Log::error('CRM: Failed to track order creation', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Track product view.
     */
    public function trackProductView(Product $product, ?Customer $customer = null, ?string $sessionId = null): bool
    {
        if (!$this->enabled) {
            return true;
        }

        try {
            $data = [
                'event' => 'product_viewed',
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_category' => $product->category,
                'product_price' => $product->price,
                'customer_id' => $customer?->id,
                'customer_email' => $customer?->email,
                'session_id' => $sessionId,
                'viewed_at' => now()->toISOString(),
            ];

            return $this->sendEvent('products/view', $data);
        } catch (\Exception $e) {
            Log::error('CRM: Failed to track product view', [
                'product_id' => $product->id,
                'customer_id' => $customer?->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Track cart abandonment.
     */
    public function trackCartAbandonment(?Customer $customer = null, ?string $sessionId = null, array $cartItems = []): bool
    {
        if (!$this->enabled || empty($cartItems)) {
            return true;
        }

        try {
            $data = [
                'event' => 'cart_abandoned',
                'customer_id' => $customer?->id,
                'customer_email' => $customer?->email,
                'session_id' => $sessionId,
                'cart_items' => collect($cartItems)->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'total' => $item->total,
                    ];
                })->toArray(),
                'cart_total' => collect($cartItems)->sum('total'),
                'abandoned_at' => now()->toISOString(),
            ];

            return $this->sendEvent('cart/abandon', $data);
        } catch (\Exception $e) {
            Log::error('CRM: Failed to track cart abandonment', [
                'customer_id' => $customer?->id,
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Sync customer data.
     */
    public function syncCustomerData(Customer $customer): bool
    {
        if (!$this->enabled) {
            return true;
        }

        try {
            $data = [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'phone' => $customer->phone,
                'total_spent' => $customer->total_spent,
                'order_count' => $customer->order_count,
                'last_order_at' => $customer->last_order_at?->toISOString(),
                'accepts_marketing' => $customer->accepts_marketing,
                'created_at' => $customer->created_at->toISOString(),
                'updated_at' => $customer->updated_at->toISOString(),
            ];

            return $this->sendEvent('customers/sync', $data);
        } catch (\Exception $e) {
            Log::error('CRM: Failed to sync customer data', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send event to CRM API.
     */
    private function sendEvent(string $endpoint, array $data): bool
    {
        try {
            $url = rtrim($this->apiUrl, '/') . '/' . ltrim($endpoint, '/');
            
            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'X-API-Secret' => $this->apiSecret,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                Log::info('CRM: Event sent successfully', [
                    'endpoint' => $endpoint,
                    'status_code' => $statusCode,
                ]);
                return true;
            }

            Log::warning('CRM: Unexpected response status', [
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'response' => $response->getBody()->getContents(),
            ]);
            return false;

        } catch (RequestException $e) {
            Log::error('CRM: HTTP request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'response' => $e->getResponse()?->getBody()?->getContents(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('CRM: Unexpected error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Test CRM connection.
     */
    public function testConnection(): array
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'CRM integration is disabled',
            ];
        }

        try {
            $url = rtrim($this->apiUrl, '/') . '/health';
            
            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'X-API-Secret' => $this->apiSecret,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                return [
                    'success' => true,
                    'message' => 'CRM connection successful',
                ];
            }

            return [
                'success' => false,
                'message' => 'CRM connection failed with status: ' . $response->getStatusCode(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'CRM connection failed: ' . $e->getMessage(),
            ];
        }
    }
}
<?php

namespace IKidnapMyself\Invoicable;

use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Webpatser\Uuid\Uuid;

class Invoice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receivable_id', 'receivable_type',  'price', 'discount', 'tax', 'currency',
        'reference', 'status', 'receiver_info', 'sender_info', 'payment_info', 'note'
    ];

    /**
     * Boot class with UUID.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query
                ->where('is_bill', false);
        });

        static::creating(function ($model) {
            if (!$model->getKey())
                $model->{$model->getKeyName()} = Uuid::generate(4)->string;

            $model->currency  = config('invoicable.default_currency');
            $model->status    = config('invoicable.default_status');
            $model->reference = InvoiceReferenceGenerator::generate();
        });
    }
    /**
     * Do not increment primary key, it's UUID.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Return primary key type for UUID.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Receivable model.
     *
     * @return mixed
     */
    public function receivable()
    {
        return $this->morphTo();
    }

    /**
     * Get the invoice lines for this invoice
     */
    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Create line.
     *
     * @param Model $model
     * @param int $price
     * @param string $description
     * @param int $tax
     * @param array $options
     * @return Illuminate\Database\Eloquent\Model
     */
    public function addLine(Model $model, int $price, string $description, int $tax, array $options = [])
    {
        $this->lines()->create([
            'invoicable_type'  => get_class($model),
            'invoicable_id'    => $model->id,
            'name'             => $description,
            'price'            => $price,
            'discount'         => array_get($options, 'discount', 0),
            'tax'              => $tax,
            'is_free'          => array_get($options, 'is_free', false),
            'is_complimentary' => array_get($options, 'is_complimentary', false),
        ]);

        return $this;
    }

    /**
     * Use this if the price does not yet include tax.
     *
     * @param Model $model
     * @param int $price
     * @param string $description
     * @param float $taxPercentage
     * @param array $options
     * @return Illuminate\Database\Eloquent\Model
     */
    public function addLineExclTax(Model $model, int $price, string $description, $taxPercentage = 0.0, array $options = [])
    {
        $tax = $price * $taxPercentage;

        return $this->addLine($model, $price + $tax, $description, $tax, $options);
    }

    /**
     * Use this if the price already includes tax.
     *
     * @param Model $model
     * @param int $price
     * @param string $description
     * @param float $taxPercentage
     * @param array $options
     * @return Illuminate\Database\Eloquent\Model
     */
    public function addLineInclTax(Model $model, int $price, string $description, $taxPercentage = 0.0, array $options = [])
    {
        $tax = $price - $price / (1 + $taxPercentage);

        return $this->addLine($model, $price, $description, $tax, $options);
    }

    /**
     * Recalculates total and tax based on lines
     * @return Illuminate\Database\Eloquent\Model  This instance
     */
    public function recalculate()
    {
        $this->price    = $this->lines()->sum('price');
        $this->tax      = $this->lines()->sum('tax');
        $this->discount = $this->lines()->sum('discount');
        $this->save();

        return $this;
    }

    /**
     * Get the View instance for the invoice.
     *
     * @param  array  $data
     * @return \Illuminate\View\View
     */
    public function view(array $data = [])
    {
        return View::make('invoicable::receipt', array_merge($data, [
            'invoice' => $this,
            'moneyFormatter' => new MoneyFormatter(
                $this->currency,
                config('invoicable.locale')
            ),
        ]));
    }

    /**
     * Capture the invoice as a PDF and return the raw bytes.
     *
     * @param  array  $data
     * @return string
     */
    public function pdf(array $data = [])
    {
        if (! defined('DOMPDF_ENABLE_AUTOLOAD')) {
            define('DOMPDF_ENABLE_AUTOLOAD', false);
        }

        if (file_exists($configPath = base_path().'/vendor/dompdf/dompdf/dompdf_config.inc.php')) {
            require_once $configPath;
        }

        $dompdf = new Dompdf;
        $dompdf->loadHtml($this->view($data)->render());
        $dompdf->render();
        return $dompdf->output();
    }

    /**
     * Create an invoice download response.
     *
     * @param  array  $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download(array $data = [])
    {
        $filename = $this->reference . '.pdf';

        return new Response($this->pdf($data), 200, [
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Type' => 'application/pdf',
        ]);
    }

    public static function findByReference($reference)
    {
        return static::where('reference', $reference)->first();
    }

    public static function findByReferenceOrFail($reference)
    {
        return static::where('reference', $reference)->firstOrFail();
    }

    public function invoicable()
    {
        return $this->morphTo();
    }
}

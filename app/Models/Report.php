<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id', 'reported_user_id', 'reported_product_id', 'order_id',
        'type', 'description', 'status', 'admin_notes', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'type'        => ReportType::class,
        'status'      => ReportStatus::class,
        'resolved_at' => 'datetime',
    ];

    public function reporter()       { return $this->belongsTo(User::class, 'reporter_id'); }
    public function reportedUser()   { return $this->belongsTo(User::class, 'reported_user_id'); }
    public function reportedProduct(){ return $this->belongsTo(Product::class, 'reported_product_id'); }
    public function order()          { return $this->belongsTo(Order::class); }
    public function resolver()       { return $this->belongsTo(User::class, 'resolved_by'); }

    public function isPending(): bool   { return $this->status === ReportStatus::Pending; }
    public function isResolved(): bool  { return $this->status === ReportStatus::Resolved; }
    public function isDismissed(): bool { return $this->status === ReportStatus::Dismissed; }
}

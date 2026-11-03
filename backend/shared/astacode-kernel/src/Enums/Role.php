<?php

declare(strict_types=1);

namespace Astacode\Kernel\Enums;

/**
 * Enum Role
 * User roles defined in ASTACODE ERP RBAC Matrix (PRD §3).
 */
enum Role: string
{
    case SUPER_ADMIN = 'super_admin';
    case DIRECTOR = 'director';
    case SALES_MANAGER = 'sales_manager';
    case SALES = 'sales';
    case MARKETING_LEAD = 'marketing_lead';
    case CONTENT_CREATOR = 'content_creator';
    case HRD_MANAGER = 'hrd_manager';
    case HRD_STAFF = 'hrd_staff';
    case PROJECT_MANAGER = 'project_manager';
    case TECH_LEAD = 'tech_lead';
    case DEVELOPER = 'developer';
    case QA_ENGINEER = 'qa_engineer';
    case FINANCE_MANAGER = 'finance_manager';
    case FINANCE_STAFF = 'finance_staff';
    case LEGAL_COUNSEL = 'legal_counsel';
    case CLIENT = 'client';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::DIRECTOR => 'Direktur Utama',
            self::SALES_MANAGER => 'Sales Manager',
            self::SALES => 'Sales Executive',
            self::MARKETING_LEAD => 'Marketing Lead',
            self::CONTENT_CREATOR => 'Content Creator',
            self::HRD_MANAGER => 'HRD Manager',
            self::HRD_STAFF => 'HRD Staff',
            self::PROJECT_MANAGER => 'Project Manager',
            self::TECH_LEAD => 'Technical Lead',
            self::DEVELOPER => 'Software Engineer',
            self::QA_ENGINEER => 'QA Engineer',
            self::FINANCE_MANAGER => 'Finance Manager',
            self::FINANCE_STAFF => 'Finance Staff',
            self::LEGAL_COUNSEL => 'Legal Counsel',
            self::CLIENT => 'Client Portal User',
        };
    }
}


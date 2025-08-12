<?php

namespace App\Support;

class CompanyIndexSql
{
    // Последнее слово ФИО: SUBSTRING_INDEX(TRIM(u.name), ' ', -1)
    protected static function managerExpr(): string
    {
        return "SUBSTRING_INDEX(TRIM(u.name), ' ', -1)";
    }

    public static function selectOne(int $companyId): string
    {
        $mgr = self::managerExpr();

        return <<<SQL
SELECT
  c.id,
  c.short_name,
  c.city,
  r.name AS region_name,
  {$mgr} AS manager_name,
  LOWER(COALESCE(GROUP_CONCAT(DISTINCT s.name  ORDER BY s.name  SEPARATOR ', '), '')) AS sectors_csv,
  LOWER(COALESCE(GROUP_CONCAT(DISTINCT e.email ORDER BY e.email SEPARATOR ', '), '')) AS emails_lc,
  COALESCE(GROUP_CONCAT(DISTINCT p.number ORDER BY p.number SEPARATOR ', '), '') AS phones_csv,
  COALESCE(GROUP_CONCAT(
    DISTINCT REGEXP_REPLACE(p.number, '[^0-9]', '') ORDER BY p.number SEPARATOR ', '
  ), '') AS phones_digits,
  NOW() AS updated_at
FROM companies c
LEFT JOIN regions r         ON r.id = c.region_id
LEFT JOIN users   u         ON u.id = c.user_id
LEFT JOIN company_sector cs ON cs.company_id = c.id
LEFT JOIN sectors s         ON s.id = cs.sector_id
LEFT JOIN phones p          ON p.company_id = c.id
LEFT JOIN emails e          ON e.company_id = c.id
WHERE c.id = {$companyId}
GROUP BY c.id, c.short_name, c.city, r.name, {$mgr}
SQL;
    }

    public static function selectAll(): string
    {
        $mgr = self::managerExpr();

        return <<<SQL
SELECT
  c.id,
  c.short_name,
  c.city,
  r.name AS region_name,
  {$mgr} AS manager_name,
  LOWER(COALESCE(GROUP_CONCAT(DISTINCT s.name  ORDER BY s.name  SEPARATOR ', '), '')) AS sectors_csv,
  LOWER(COALESCE(GROUP_CONCAT(DISTINCT e.email ORDER BY e.email SEPARATOR ', '), '')) AS emails_lc,
  COALESCE(GROUP_CONCAT(DISTINCT p.number ORDER BY p.number SEPARATOR ', '), '') AS phones_csv,
  COALESCE(GROUP_CONCAT(
    DISTINCT REGEXP_REPLACE(p.number, '[^0-9]', '') ORDER BY p.number SEPARATOR ', '
  ), '') AS phones_digits,
  NOW() AS updated_at
FROM companies c
LEFT JOIN regions r         ON r.id = c.region_id
LEFT JOIN users   u         ON u.id = c.user_id
LEFT JOIN company_sector cs ON cs.company_id = c.id
LEFT JOIN sectors s         ON s.id = cs.sector_id
LEFT JOIN phones p          ON p.company_id = c.id
LEFT JOIN emails e          ON e.company_id = c.id
GROUP BY c.id, c.short_name, c.city, r.name, {$mgr}
SQL;
    }
}

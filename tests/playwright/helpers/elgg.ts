import { Page } from '@playwright/test';
import mysql from 'mysql2/promise';

const DB_CONFIG = {
  host: process.env.ELGG_DB_HOST || 'db',
  port: Number(process.env.ELGG_DB_PORT || 3306),
  user: process.env.ELGG_DB_USER || 'elgg',
  password: process.env.ELGG_DB_PASS || 'elgg',
  database: process.env.ELGG_DB_NAME || 'elgg',
};

export async function loginAs(
  page: Page,
  username: string,
  password: string = 'testpass123'
) {
  await page.goto('/login');
  await page.fill('input[name="username"]', username);
  await page.fill('input[name="password"]', password);
  await page.click('input[type="submit"], button[type="submit"]');
  await page.waitForURL(/\//);
}

export async function queryDb(sql: string, params: any[] = []): Promise<any[]> {
  const conn = await mysql.createConnection(DB_CONFIG);
  try {
    const [rows] = await conn.execute(sql, params);
    return rows as any[];
  } finally {
    await conn.end();
  }
}

export async function getUserByUsername(username: string): Promise<any | null> {
  const rows = await queryDb(
    `SELECT e.guid, e.type, e.subtype, u.username
     FROM elgg_entities e
     JOIN elgg_users_entity u ON u.guid = e.guid
     WHERE u.username = ?`,
    [username]
  );
  return rows[0] || null;
}

export async function getMetadata(entityGuid: number, name: string): Promise<any[]> {
  return queryDb(
    `SELECT name, value FROM elgg_metadata WHERE entity_guid = ? AND name = ?`,
    [entityGuid, name]
  );
}

export async function getPluginSetting(pluginId: string, name: string): Promise<string | null> {
  // Plugin settings in Elgg 4 are stored as private settings on the plugin entity.
  const rows = await queryDb(
    `SELECT ps.value
     FROM elgg_private_settings ps
     JOIN elgg_entities e ON e.guid = ps.entity_guid
     JOIN elgg_plugins_entity p ON p.guid = e.guid
     WHERE p.title = ? AND ps.name = ?`,
    [pluginId, name]
  );
  return rows[0]?.value ?? null;
}

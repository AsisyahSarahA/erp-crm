import React, { useState } from 'react'
import { formatIDR } from '../utils/formatters'
import { Plus, Trash2, Eye, EyeOff } from 'lucide-react'

export interface RabItem {
  id: string
  category: string
  role: string
  name: string
  mandays: number
  internalRate: number // Cost per manday (Internal)
  marginPercent: number // e.g., 30 for 30%
  clientRate: number // Final client rate per manday
}

interface SpreadsheetGridProps {
  items: RabItem[]
  onChange: (items: RabItem[]) => void
  readOnly?: boolean
  initialInternalMode?: boolean
}

export const SpreadsheetGrid: React.FC<SpreadsheetGridProps> = ({
  items,
  onChange,
  readOnly = false,
  initialInternalMode = true,
}) => {
  const [internalMode, setInternalMode] = useState(initialInternalMode)

  const handleUpdateItem = (id: string, field: keyof RabItem, value: any) => {
    const updated = items.map((item) => {
      if (item.id !== id) return item

      const newItem = { ...item, [field]: value }

      // Auto recalculate clientRate if internalRate or marginPercent changed
      if (field === 'internalRate' || field === 'marginPercent') {
        const cost = field === 'internalRate' ? Number(value) : item.internalRate
        const margin = field === 'marginPercent' ? Number(value) : item.marginPercent
        newItem.clientRate = Math.round(cost * (1 + margin / 100))
      }

      // If user edits clientRate directly in client view, recompute margin %
      if (field === 'clientRate' && item.internalRate > 0) {
        const clientVal = Number(value)
        newItem.marginPercent = Math.round(((clientVal - item.internalRate) / item.internalRate) * 100)
      }

      return newItem
    })
    onChange(updated)
  }

  const handleAddRow = () => {
    const newItem: RabItem = {
      id: `item-${Date.now()}`,
      category: 'Development',
      role: 'Fullstack Dev',
      name: 'Fitur Baru',
      mandays: 1,
      internalRate: 1500000,
      marginPercent: 30,
      clientRate: 1950000,
    }
    onChange([...items, newItem])
  }

  const handleDeleteRow = (id: string) => {
    onChange(items.filter((item) => item.id !== id))
  }

  // Summary calculations
  const totalMandays = items.reduce((sum, i) => sum + (Number(i.mandays) || 0), 0)
  const totalInternalCost = items.reduce(
    (sum, i) => sum + (Number(i.mandays) || 0) * (Number(i.internalRate) || 0),
    0
  )
  const totalClientPrice = items.reduce(
    (sum, i) => sum + (Number(i.mandays) || 0) * (Number(i.clientRate) || 0),
    0
  )
  const totalGrossMargin = totalClientPrice > 0 ? ((totalClientPrice - totalInternalCost) / totalClientPrice) * 100 : 0

  return (
    <div className="w-full space-y-4">
      {/* Controls */}
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={() => setInternalMode(!internalMode)}
            className="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
          >
            {internalMode ? (
              <>
                <EyeOff className="h-3.5 w-3.5 text-slate-400" />
                <span>Mode: Internal (Cost & Margin Terlihat)</span>
              </>
            ) : (
              <>
                <Eye className="h-3.5 w-3.5 text-indigo-500" />
                <span>Mode: Klien (Hanya Rate Final)</span>
              </>
            )}
          </button>
        </div>

        {!readOnly && (
          <button
            type="button"
            onClick={handleAddRow}
            className="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-indigo-700"
          >
            <Plus className="h-3.5 w-3.5" /> Tambah Baris RAB
          </button>
        )}
      </div>

      {/* Spreadsheet Grid Table */}
      <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
        <table className="w-full text-left text-xs text-slate-700 dark:text-slate-300">
          <thead className="border-b border-slate-200 bg-slate-50 font-semibold text-slate-600 uppercase tracking-wider dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-400">
            <tr>
              <th className="px-3 py-2.5">Kategori</th>
              <th className="px-3 py-2.5">Peran / Role</th>
              <th className="px-3 py-2.5">Fitur / Lingkup Kerja</th>
              <th className="px-3 py-2.5 text-right w-20">Mandays</th>

              {internalMode && (
                <>
                  <th className="px-3 py-2.5 text-right w-32">Cost / Manday</th>
                  <th className="px-3 py-2.5 text-right w-20">Margin (%)</th>
                </>
              )}

              <th className="px-3 py-2.5 text-right w-36">Rate Klien / Manday</th>
              <th className="px-3 py-2.5 text-right w-36">Subtotal</th>
              {!readOnly && <th className="px-2 py-2.5 w-10 text-center">Aksi</th>}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100 dark:divide-slate-800 font-mono">
            {items.map((item) => {
              const rowSubtotal = (Number(item.mandays) || 0) * (Number(item.clientRate) || 0)

              return (
                <tr key={item.id} className="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                  {/* Category */}
                  <td className="p-1 font-sans">
                    <input
                      disabled={readOnly}
                      value={item.category}
                      onChange={(e) => handleUpdateItem(item.id, 'category', e.target.value)}
                      className="w-full rounded px-2 py-1 text-xs border border-transparent hover:border-slate-300 focus:border-indigo-500 bg-transparent"
                    />
                  </td>

                  {/* Role */}
                  <td className="p-1 font-sans">
                    <input
                      disabled={readOnly}
                      value={item.role}
                      onChange={(e) => handleUpdateItem(item.id, 'role', e.target.value)}
                      className="w-full rounded px-2 py-1 text-xs border border-transparent hover:border-slate-300 focus:border-indigo-500 bg-transparent"
                    />
                  </td>

                  {/* Name */}
                  <td className="p-1 font-sans">
                    <input
                      disabled={readOnly}
                      value={item.name}
                      onChange={(e) => handleUpdateItem(item.id, 'name', e.target.value)}
                      className="w-full rounded px-2 py-1 text-xs border border-transparent hover:border-slate-300 focus:border-indigo-500 bg-transparent font-medium"
                    />
                  </td>

                  {/* Mandays */}
                  <td className="p-1 text-right">
                    <input
                      disabled={readOnly}
                      type="number"
                      min={0.5}
                      step={0.5}
                      value={item.mandays}
                      onChange={(e) => handleUpdateItem(item.id, 'mandays', parseFloat(e.target.value) || 0)}
                      className="w-full rounded px-2 py-1 text-right text-xs border border-transparent hover:border-slate-300 focus:border-indigo-500 bg-transparent"
                    />
                  </td>

                  {/* Internal Cost & Margin */}
                  {internalMode && (
                    <>
                      <td className="p-1 text-right">
                        <input
                          disabled={readOnly}
                          type="number"
                          value={item.internalRate}
                          onChange={(e) => handleUpdateItem(item.id, 'internalRate', parseInt(e.target.value, 10) || 0)}
                          className="w-full rounded px-2 py-1 text-right text-xs border border-transparent hover:border-slate-300 focus:border-indigo-500 bg-transparent text-slate-500"
                        />
                      </td>
                      <td className="p-1 text-right">
                        <input
                          disabled={readOnly}
                          type="number"
                          value={item.marginPercent}
                          onChange={(e) => handleUpdateItem(item.id, 'marginPercent', parseFloat(e.target.value) || 0)}
                          className="w-full rounded px-2 py-1 text-right text-xs border border-transparent hover:border-slate-300 focus:border-indigo-500 bg-transparent text-indigo-600 font-semibold"
                        />
                      </td>
                    </>
                  )}

                  {/* Client Rate */}
                  <td className="p-1 text-right">
                    <input
                      disabled={readOnly}
                      type="number"
                      value={item.clientRate}
                      onChange={(e) => handleUpdateItem(item.id, 'clientRate', parseInt(e.target.value, 10) || 0)}
                      className="w-full rounded px-2 py-1 text-right text-xs border border-transparent hover:border-slate-300 focus:border-indigo-500 bg-transparent font-semibold text-slate-800 dark:text-slate-100"
                    />
                  </td>

                  {/* Subtotal */}
                  <td className="px-3 py-2 text-right font-semibold text-slate-900 dark:text-slate-100">
                    {formatIDR(rowSubtotal)}
                  </td>

                  {/* Actions */}
                  {!readOnly && (
                    <td className="p-1 text-center font-sans">
                      <button
                        type="button"
                        onClick={() => handleDeleteRow(item.id)}
                        className="rounded p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50"
                      >
                        <Trash2 className="h-3.5 w-3.5" />
                      </button>
                    </td>
                  )}
                </tr>
              )
            })}
          </tbody>
          <tfoot className="border-t-2 border-slate-300 bg-slate-50 font-bold dark:border-slate-700 dark:bg-slate-950/80">
            <tr>
              <td colSpan={3} className="px-3 py-3 font-sans text-xs uppercase">
                Total Ringkasan Estimasi
              </td>
              <td className="px-3 py-3 text-right font-mono text-indigo-600">{totalMandays} MD</td>

              {internalMode && (
                <>
                  <td className="px-3 py-3 text-right font-mono text-slate-500">
                    Cost: {formatIDR(totalInternalCost)}
                  </td>
                  <td className="px-3 py-3 text-right font-mono text-emerald-600">
                    {totalGrossMargin.toFixed(1)}%
                  </td>
                </>
              )}

              <td className="px-3 py-3 text-right font-sans text-xs uppercase">Total Nilai Klien:</td>
              <td className="px-3 py-3 text-right font-mono text-sm text-indigo-600 dark:text-indigo-400">
                {formatIDR(totalClientPrice)}
              </td>
              {!readOnly && <td />}
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  )
}


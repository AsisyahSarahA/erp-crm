import React, { useState, useMemo } from 'react'
import { cn } from '../utils/cn'
import { ChevronUp, ChevronDown, ChevronsUpDown, ChevronLeft, ChevronRight, Search } from 'lucide-react'

export interface ColumnDef<T> {
  id: string
  header: string | React.ReactNode
  accessor?: (row: T) => any
  cell?: (row: T, index: number) => React.ReactNode
  sortable?: boolean
  className?: string
}

interface DataTableProps<T> {
  data: T[]
  columns: ColumnDef<T>[]
  keyExtractor: (row: T) => string | number
  searchable?: boolean
  searchPlaceholder?: string
  searchFields?: (keyof T | ((row: T) => string))[]
  pageSizeOptions?: number[]
  defaultPageSize?: number
  onRowClick?: (row: T) => void
  bulkActions?: (selectedRows: T[]) => React.ReactNode
  emptyMessage?: string
  className?: string
}

export function DataTable<T>({
  data,
  columns,
  keyExtractor,
  searchable = true,
  searchPlaceholder = 'Cari data...',
  searchFields,
  pageSizeOptions = [10, 25, 50],
  defaultPageSize = 10,
  onRowClick,
  bulkActions,
  emptyMessage = 'Tidak ada data ditemukan.',
  className,
}: DataTableProps<T>) {
  const [searchQuery, setSearchQuery] = useState('')
  const [sortColumn, setSortColumn] = useState<string | null>(null)
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('asc')
  const [currentPage, setCurrentPage] = useState(1)
  const [pageSize, setPageSize] = useState(defaultPageSize)
  const [selectedKeys, setSelectedKeys] = useState<Set<string | number>>(new Set())

  // Search filtering
  const filteredData = useMemo(() => {
    if (!searchQuery.trim()) return data

    const query = searchQuery.toLowerCase().trim()
    return data.filter((row) => {
      if (searchFields && searchFields.length > 0) {
        return searchFields.some((field) => {
          const val = typeof field === 'function' ? field(row) : (row as any)[field]
          return String(val ?? '').toLowerCase().includes(query)
        })
      }
      // Default: inspect all columns accessor or string values
      return columns.some((col) => {
        const val = col.accessor ? col.accessor(row) : (row as any)[col.id]
        return String(val ?? '').toLowerCase().includes(query)
      })
    })
  }, [data, searchQuery, searchFields, columns])

  // Sorting
  const sortedData = useMemo(() => {
    if (!sortColumn) return filteredData

    const col = columns.find((c) => c.id === sortColumn)
    if (!col) return filteredData

    return [...filteredData].sort((a, b) => {
      const valA = col.accessor ? col.accessor(a) : (a as any)[col.id]
      const valB = col.accessor ? col.accessor(b) : (b as any)[col.id]

      if (valA === valB) return 0
      if (valA === null || valA === undefined) return 1
      if (valB === null || valB === undefined) return -1

      const result = valA < valB ? -1 : 1
      return sortDirection === 'asc' ? result : -result
    })
  }, [filteredData, sortColumn, sortDirection, columns])

  // Pagination
  const totalPages = Math.ceil(sortedData.length / pageSize) || 1
  const paginatedData = useMemo(() => {
    const start = (currentPage - 1) * pageSize
    return sortedData.slice(start, start + pageSize)
  }, [sortedData, currentPage, pageSize])

  const handleSort = (colId: string) => {
    if (sortColumn === colId) {
      if (sortDirection === 'asc') setSortDirection('desc')
      else {
        setSortColumn(null)
        setSortDirection('asc')
      }
    } else {
      setSortColumn(colId)
      setSortDirection('asc')
    }
  }

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      const all = new Set(paginatedData.map((r) => keyExtractor(r)))
      setSelectedKeys(all)
    } else {
      setSelectedKeys(new Set())
    }
  }

  const handleSelectRow = (key: string | number) => {
    const next = new Set(selectedKeys)
    if (next.has(key)) next.delete(key)
    else next.add(key)
    setSelectedKeys(next)
  }

  const selectedRows = useMemo(() => {
    return data.filter((row) => selectedKeys.has(keyExtractor(row)))
  }, [data, selectedKeys, keyExtractor])

  return (
    <div className={cn('w-full space-y-3', className)}>
      {/* Top Bar: Search & Bulk Actions */}
      <div className="flex flex-col sm:flex-row items-center justify-between gap-3">
        {searchable ? (
          <div className="relative w-full sm:w-72">
            <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
            <input
              type="text"
              placeholder={searchPlaceholder}
              value={searchQuery}
              onChange={(e) => {
                setSearchQuery(e.target.value)
                setCurrentPage(1)
              }}
              className="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            />
          </div>
        ) : <div />}

        {bulkActions && selectedKeys.size > 0 && (
          <div className="flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
            <span>{selectedKeys.size} baris dipilih:</span>
            {bulkActions(selectedRows)}
          </div>
        )}
      </div>

      {/* Table Container */}
      <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
        <table className="w-full text-left text-sm text-slate-700 dark:text-slate-300">
          <thead className="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider dark:border-slate-800 dark:bg-slate-950/50 dark:text-slate-400">
            <tr>
              {bulkActions && (
                <th className="w-10 px-4 py-3">
                  <input
                    type="checkbox"
                    className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    checked={paginatedData.length > 0 && paginatedData.every((r) => selectedKeys.has(keyExtractor(r)))}
                    onChange={(e) => handleSelectAll(e.target.checked)}
                  />
                </th>
              )}
              {columns.map((col) => (
                <th
                  key={col.id}
                  className={cn(
                    'px-4 py-3 select-none',
                    col.sortable && 'cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800',
                    col.className
                  )}
                  onClick={() => col.sortable && handleSort(col.id)}
                >
                  <div className="flex items-center gap-1.5">
                    <span>{col.header}</span>
                    {col.sortable && (
                      <span className="text-slate-400">
                        {sortColumn === col.id ? (
                          sortDirection === 'asc' ? (
                            <ChevronUp className="h-3.5 w-3.5 text-indigo-600" />
                          ) : (
                            <ChevronDown className="h-3.5 w-3.5 text-indigo-600" />
                          )
                        ) : (
                          <ChevronsUpDown className="h-3.5 w-3.5" />
                        )}
                      </span>
                    )}
                  </div>
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
            {paginatedData.length === 0 ? (
              <tr>
                <td
                  colSpan={columns.length + (bulkActions ? 1 : 0)}
                  className="px-4 py-8 text-center text-sm text-slate-400 dark:text-slate-500"
                >
                  {emptyMessage}
                </td>
              </tr>
            ) : (
              paginatedData.map((row, index) => {
                const key = keyExtractor(row)
                const isSelected = selectedKeys.has(key)
                return (
                  <tr
                    key={key}
                    onClick={() => onRowClick && onRowClick(row)}
                    className={cn(
                      'transition-colors hover:bg-indigo-50/40 dark:hover:bg-indigo-950/20',
                      onRowClick && 'cursor-pointer',
                      isSelected && 'bg-indigo-50/60 dark:bg-indigo-950/40'
                    )}
                  >
                    {bulkActions && (
                      <td className="w-10 px-4 py-3" onClick={(e) => e.stopPropagation()}>
                        <input
                          type="checkbox"
                          className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                          checked={isSelected}
                          onChange={() => handleSelectRow(key)}
                        />
                      </td>
                    )}
                    {columns.map((col) => (
                      <td key={col.id} className={cn('px-4 py-3.5', col.className)}>
                        {col.cell ? col.cell(row, index) : col.accessor ? col.accessor(row) : (row as any)[col.id]}
                      </td>
                    ))}
                  </tr>
                )
              })
            )}
          </tbody>
        </table>
      </div>

      {/* Pagination Bar */}
      <div className="flex flex-col sm:flex-row items-center justify-between gap-3 px-1 text-xs text-slate-500 dark:text-slate-400">
        <div>
          Menampilkan {(currentPage - 1) * pageSize + 1} -{' '}
          {Math.min(currentPage * pageSize, sortedData.length)} dari {sortedData.length} data
        </div>

        <div className="flex items-center gap-4">
          <div className="flex items-center gap-1.5">
            <span>Baris:</span>
            <select
              value={pageSize}
              onChange={(e) => {
                setPageSize(Number(e.target.value))
                setCurrentPage(1)
              }}
              className="rounded border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200"
            >
              {pageSizeOptions.map((opt) => (
                <option key={opt} value={opt}>
                  {opt}
                </option>
              ))}
            </select>
          </div>

          <div className="flex items-center gap-1">
            <button
              disabled={currentPage <= 1}
              onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
              className="rounded p-1 text-slate-600 hover:bg-slate-100 disabled:opacity-30 dark:text-slate-300 dark:hover:bg-slate-800"
            >
              <ChevronLeft className="h-4 w-4" />
            </button>
            <span className="px-2 font-medium">
              {currentPage} / {totalPages}
            </span>
            <button
              disabled={currentPage >= totalPages}
              onClick={() => setCurrentPage((p) => Math.min(totalPages, p + 1))}
              className="rounded p-1 text-slate-600 hover:bg-slate-100 disabled:opacity-30 dark:text-slate-300 dark:hover:bg-slate-800"
            >
              <ChevronRight className="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}


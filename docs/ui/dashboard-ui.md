# User dashboard UI conventions

Applies to every view under `resources/views/dashboard/**` (layout: `dashboard.layout.app`).
Tailwind (CDN) with the Prime palette from `partials/brand-theme`: `gray-*` is the navy scale
(950 page → 800 card → 700 border), `gold-*` is the blue accent, `blue-600` primary buttons.

## Page header (every page)
```blade
<div class="flex flex-wrap items-end justify-between gap-3">
  <div>
    <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">{{ Section }}</p>
    <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">{{ Page title }}</h1>
    <p class="text-sm text-gray-400 mt-0.5">{{ One sentence: what you can do here }}</p>
  </div>
  <div class="flex items-center gap-2"><!-- primary action button / back link --></div>
</div>
```
Page wrapper: `<div class="space-y-4 sm:space-y-6">`. No `min-h-screen`/`bg-gray-900` wrappers (the layout provides them).

## Surfaces
- Card: `rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6`
- Inner tile: `rounded-xl border border-gray-700 bg-gray-900/50 p-4`
- Section title inside a card: `text-base font-semibold text-white`, optional action link `text-sm font-semibold text-gold-400 hover:text-gold-300`
- Stat tile: label `text-xs text-gray-400`, value `text-xl sm:text-2xl font-semibold text-white` (money in `font-mono` is fine)
- Grids: `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4` (never fixed multi-column on phones)

## Buttons
- Primary: `inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold`
- Secondary: same shape, `bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white`
- Danger: `bg-red-600 hover:bg-red-700` (or outline `border border-red-500/40 text-red-400`)
- Full width on phones where it is the main action: `w-full sm:w-auto`
- No gradients (`bg-gradient-*`), no purple accents, no `transform hover:scale-*`.

## Tables on phones
Pick one:
1. Cards on phones + table from md: `<ul class="md:hidden divide-y divide-gray-700">…</ul>` and `<div class="hidden md:block overflow-x-auto"><table class="min-w-full text-sm">…`
2. Simple tables: wrap in `overflow-x-auto` and give the table `min-w-[640px]` so it scrolls inside its card, never the page.
Status badges: `px-2 py-0.5 rounded-md text-xs font-semibold` + `bg-emerald-500/15 text-emerald-400` (success) / `bg-amber-500/15 text-amber-300` (pending) / `bg-red-500/15 text-red-400` (failed) / `bg-gray-700 text-gray-300` (neutral).

## Forms
- Label `block text-sm font-medium text-gray-300 mb-1.5`; input `w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500`
- Money inputs: `inputmode="decimal"`, currency symbol from `currency_symbol()` as a prefix.
- Two-column fields only from `sm:` (`grid grid-cols-1 sm:grid-cols-2 gap-4`).
- Error text `text-sm text-red-400`; help text `text-xs text-gray-500`.

## Modals
Bottom sheet on phones, centred dialog from sm:
`fixed inset-0 z-50 bg-black/60 backdrop-blur-sm` → inner `flex items-end sm:items-center justify-center min-h-full sm:p-4` → panel `w-full max-w-lg rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto`.
Confirmations/notices: `uiConfirm()` / `uiAlert()` / `data-confirm` (partials/dialogs) — never `alert()`/`confirm()`.

## Empty and loading states
Centered: icon in `w-12 h-12 rounded-full bg-gray-700 grid place-items-center`, title `text-white font-medium`, one line `text-sm text-gray-400`, and the next action button when there is one.

## Money and data
Amounts via `money()`, `money_from()` or `$user->formatAmount()` (never a hard-coded `$`). Don't change routes, form field names, IDs or JS hooks used by scripts.

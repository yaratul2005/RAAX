# Reusable master-detail module (WPF / MVVM)

A reference implementation of the List → Toolbar → Form pattern described earlier,
built around the Invoicing module. Copy the `Common/` folder as-is into your project;
copy the `Modules/Invoicing/` and `Views/` folders as the template for every other
module (Purchase, Inventory, HR...).

## How the pieces fit

```
Common/                        <- write once, never touch again per module
  ObservableObject.cs          base INotifyPropertyChanged
  ValidatableModel.cs          base INotifyDataErrorInfo (native WPF validation)
  RelayCommand.cs               generic ICommand
  ListViewModelBase.cs         New/Edit/Delete/Refresh/Search plumbing for any list screen
  DetailViewModelBase.cs       Save/Cancel gate + Title + Closed event for any edit screen
  NullToVisibilityConverter.cs

Modules/Invoicing/             <- the part that's actually different per module
  InvoiceHeader.cs             model + validation rules (inherits ValidatableModel)
  InvoiceLine.cs                same, for the line-item grid
  InvoiceListViewModel.cs      ~10 lines: implements LoadItemsAsync/DeleteItemAsync
  InvoiceDetailViewModel.cs    ~15 lines: implements PersistAsync + line commands
  InvoiceShellViewModel.cs     composes List + Detail, wires the New/Edit swap

Views/
  InvoiceModuleView.xaml       list (left) + docked detail panel (right)
  InvoiceDetailForm.xaml       the actual form, with inline validation messages

Resources/
  ValidationStyles.xaml        red-border-on-error styling, shared by every TextBox/DatePicker
```

The point of splitting it this way: when you build the Purchase Order module next, you
write a new `PurchaseOrderHeader : ValidatableModel`, a new `PurchaseOrderListViewModel`
(implementing two methods), a new `PurchaseOrderDetailViewModel`, and a new XAML form.
Everything else - selection handling, delete confirmation, the Save gate, the docked
panel behavior - comes from `Common/` for free.

## Validation pattern, summarized

1. Models inherit `ValidatableModel`, not just `ObservableObject`.
2. Any field with a rule calls `SetPropertyValidated` instead of `SetProperty`.
3. `ValidateProperty(propertyName)` runs on every keystroke (via `UpdateSourceTrigger=
   PropertyChanged`) - this is what gives live, per-field feedback instead of a Save-time
   popup dump.
4. `ValidateAll()` runs once more on Save, to catch required fields the user never
   touched (they never fired step 3, since the setter never ran).
5. In XAML, the error message is a `TextBlock` bound to
   `{Binding ElementName=TheField, Path=(Validation.Errors)[0].ErrorContent}`, shown
   only while `Validation.HasError` is true. This puts the message *below the field*
   in the layout (reserves space, no floating box), matching the "inline, at the field
   level" convention from the UX guidance.
6. `SaveCommand` disables itself the instant `Item.HasErrors` becomes true (wired via
   the model's `ErrorsChanged` event in `DetailViewModelBase`), so the button greys out
   live as the user types - they never even reach a rejected Save.

## Wiring notes (not included, project-specific)

- **DI / composition root**: `InvoiceShellViewModel` takes an `IInvoiceRepository` in
  its constructor. Wire that up in `App.xaml.cs` (or your DI container of choice) with
  a concrete EF Core / Dapper implementation.
- **Navigation**: the sidebar in the earlier shell mockup would hold one
  `InvoiceShellViewModel`-per-module instance, swapped into a `ContentControl` in the
  main window when the user clicks a sidebar item.
- **Cell-level grid validation**: the line-items `DataGrid` currently only validates
  on Save (`InvoiceLine.ValidateAll()`); a red cell background per invalid row uses the
  same `Validation.HasError` trigger technique, scoped to a `DataGridCell` style with
  `RelativeSource={RelativeSource Self}`.

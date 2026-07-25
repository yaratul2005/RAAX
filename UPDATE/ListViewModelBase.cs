using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Threading.Tasks;
using System.Windows;

namespace ErpSample.Common
{
    /// <summary>
    /// Reusable base for every "List" screen - invoices, customers, items, purchase orders.
    /// A concrete module only implements LoadItemsAsync() and DeleteItemAsync(); New/Edit/
    /// Delete/Refresh wiring, selection tracking, and the double-click-to-edit contract are
    /// all inherited for free. This is the piece that keeps every module's list screen
    /// behaving identically.
    /// </summary>
    public abstract class ListViewModelBase<TModel> : ObservableObject where TModel : class
    {
        private TModel? _selectedItem;
        private string _searchText = string.Empty;

        public ObservableCollection<TModel> Items { get; } = new();

        public TModel? SelectedItem
        {
            get => _selectedItem;
            set { SetProperty(ref _selectedItem, value); RelayCommand.RaiseCanExecuteChanged(); }
        }

        public string SearchText
        {
            get => _searchText;
            set { if (SetProperty(ref _searchText, value)) _ = LoadAsync(); }
        }

        public RelayCommand NewCommand { get; }
        public RelayCommand EditCommand { get; }
        public RelayCommand DeleteCommand { get; }
        public RelayCommand RefreshCommand { get; }

        /// <summary>
        /// Raised with null for New, or the selected record for Edit. The shell view model
        /// (see InvoiceShellViewModel) listens to this and swaps in the detail panel.
        /// </summary>
        public event Action<TModel?>? EditRequested;

        protected ListViewModelBase()
        {
            NewCommand = new RelayCommand(_ => EditRequested?.Invoke(null));
            EditCommand = new RelayCommand(_ => EditRequested?.Invoke(SelectedItem), _ => SelectedItem != null);
            DeleteCommand = new RelayCommand(async _ => await DeleteSelectedAsync(), _ => SelectedItem != null);
            RefreshCommand = new RelayCommand(async _ => await LoadAsync());
        }

        public async Task LoadAsync()
        {
            // Real implementation re-queries the repository using SearchText server-side
            // instead of filtering an in-memory list - important once a table has
            // thousands of rows.
            var results = await LoadItemsAsync();
            Items.Clear();
            foreach (var item in results)
                Items.Add(item);
        }

        private async Task DeleteSelectedAsync()
        {
            if (SelectedItem == null) return;

            var confirm = MessageBox.Show(
                "Delete the selected record? This cannot be undone.",
                "Confirm delete", MessageBoxButton.YesNo, MessageBoxImage.Warning);
            if (confirm != MessageBoxResult.Yes) return;

            await DeleteItemAsync(SelectedItem);
            Items.Remove(SelectedItem);
            SelectedItem = null;
        }

        protected abstract Task<IEnumerable<TModel>> LoadItemsAsync();
        protected abstract Task DeleteItemAsync(TModel item);
    }
}

using System;
using System.Collections;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Threading.Tasks;
using System.Windows;

namespace ErpSample.Common
{
    public abstract class ListViewModelBase<TModel> : ObservableObject where TModel : class
    {
        private TModel _selectedItem;
        private string _searchText = string.Empty;

        public ObservableCollection<TModel> Items { get; private set; }

        public TModel SelectedItem
        {
            get { return _selectedItem; }
            set 
            { 
                SetProperty(ref _selectedItem, value, "SelectedItem"); 
                RelayCommand.RaiseCanExecuteChanged(); 
            }
        }

        public string SearchText
        {
            get { return _searchText; }
            set 
            { 
                if (SetProperty(ref _searchText, value, "SearchText")) 
                    LoadAsync(); 
            }
        }

        public RelayCommand NewCommand { get; private set; }
        public RelayCommand EditCommand { get; private set; }
        public RelayCommand DeleteCommand { get; private set; }
        public RelayCommand RefreshCommand { get; private set; }

        public event Action<TModel> EditRequested;

        protected ListViewModelBase()
        {
            Items = new ObservableCollection<TModel>();
            NewCommand = new RelayCommand(param => { if (EditRequested != null) EditRequested(null); });
            EditCommand = new RelayCommand(param => { if (EditRequested != null) EditRequested(SelectedItem); }, param => SelectedItem != null);
            DeleteCommand = new RelayCommand(async param => await DeleteSelectedAsync(), param => SelectedItem != null);
            RefreshCommand = new RelayCommand(async param => await LoadAsync());
        }

        public async Task LoadAsync()
        {
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

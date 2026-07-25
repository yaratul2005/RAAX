using System;
using System.Threading.Tasks;

namespace ErpSample.Common
{
    public abstract class DetailViewModelBase<TModel> : ObservableObject where TModel : ValidatableModel
    {
        private TModel _item;
        private bool _isNew;

        protected DetailViewModelBase(TModel item, bool isNew)
        {
            _item = item;
            _isNew = isNew;

            _item.ErrorsChanged += (sender, e) => RelayCommand.RaiseCanExecuteChanged();

            SaveCommand = new RelayCommand(async param => await SaveAsync(), param => !Item.HasErrors);
            CancelCommand = new RelayCommand(param => { if (Closed != null) Closed(); });
        }

        public TModel Item
        {
            get { return _item; }
            set { SetProperty(ref _item, value, "Item"); }
        }

        public bool IsNew
        {
            get { return _isNew; }
            private set 
            { 
                SetProperty(ref _isNew, value, "IsNew"); 
                OnPropertyChanged("Title"); 
            }
        }

        public string Title 
        {
            get { return IsNew ? "New record" : "Edit record"; }
        }

        public RelayCommand SaveCommand { get; private set; }
        public RelayCommand CancelCommand { get; private set; }

        public event Action Closed;

        private async Task SaveAsync()
        {
            if (!Item.ValidateAll())
                return;

            await PersistAsync(Item);
            IsNew = false;
            if (Closed != null) Closed();
        }

        protected abstract Task PersistAsync(TModel item);
    }
}

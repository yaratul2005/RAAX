using System;
using System.Threading.Tasks;

namespace ErpSample.Common
{
    /// <summary>
    /// Reusable base for every "Detail / Edit" screen. Handles the New-vs-Edit title, the
    /// Save gate (SaveCommand disables itself the instant the model has any error, and
    /// re-validates everything on Save in case a required field was never touched), and
    /// the Closed event that tells the shell to swap back to the list.
    /// </summary>
    public abstract class DetailViewModelBase<TModel> : ObservableObject where TModel : ValidatableModel
    {
        private TModel _item;
        private bool _isNew;

        protected DetailViewModelBase(TModel item, bool isNew)
        {
            _item = item;
            _isNew = isNew;

            // Re-check CanExecute every time the model's error state changes, so the
            // Save button greys out live as the user types, not just at submit time.
            _item.ErrorsChanged += (_, __) => RelayCommand.RaiseCanExecuteChanged();

            SaveCommand = new RelayCommand(async _ => await SaveAsync(), _ => !Item.HasErrors);
            CancelCommand = new RelayCommand(_ => Closed?.Invoke());
        }

        public TModel Item
        {
            get => _item;
            set => SetProperty(ref _item, value);
        }

        public bool IsNew
        {
            get => _isNew;
            private set { SetProperty(ref _isNew, value); OnPropertyChanged(nameof(Title)); }
        }

        public string Title => IsNew ? "New record" : "Edit record";

        public RelayCommand SaveCommand { get; }
        public RelayCommand CancelCommand { get; }

        /// <summary>Shell view model listens to this and swaps the detail panel back out.</summary>
        public event Action? Closed;

        private async Task SaveAsync()
        {
            // Belt and braces: SaveCommand is already disabled while HasErrors is true,
            // but ValidateAll() also catches required fields the user tabbed past
            // without ever entering a value (which never fired SetPropertyValidated).
            if (!Item.ValidateAll())
                return;

            await PersistAsync(Item);
            IsNew = false;
            Closed?.Invoke();
        }

        /// <summary>Concrete module implements the actual repository/database call.</summary>
        protected abstract Task PersistAsync(TModel item);
    }
}

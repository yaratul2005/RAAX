using ErpSample.Common;

namespace ErpSample.Modules.Invoicing
{
    /// <summary>
    /// Composes the List and Detail view models for one module. The main window binds one
    /// instance of a shell VM like this per module (InvoiceShellViewModel, CustomerShellViewModel,
    /// StockItemShellViewModel...). This is the piece that actually makes New/Edit swap the
    /// right-hand panel in InvoiceModuleView.xaml - copy this class per module, nothing else
    /// in Common changes.
    /// </summary>
    public class InvoiceShellViewModel : ObservableObject
    {
        private readonly IInvoiceRepository _repository;
        private InvoiceDetailViewModel? _detailViewModel;

        public InvoiceListViewModel ListViewModel { get; }

        public InvoiceDetailViewModel? DetailViewModel
        {
            get => _detailViewModel;
            private set => SetProperty(ref _detailViewModel, value);
        }

        public InvoiceShellViewModel(IInvoiceRepository repository)
        {
            _repository = repository;
            ListViewModel = new InvoiceListViewModel(repository);
            ListViewModel.EditRequested += OnEditRequested;
        }

        private void OnEditRequested(InvoiceHeader? invoice)
        {
            var isNew = invoice == null;
            var item = invoice ?? new InvoiceHeader();

            var detail = new InvoiceDetailViewModel(item, isNew, _repository);
            detail.Closed += async () =>
            {
                DetailViewModel = null;
                await ListViewModel.LoadAsync(); // refresh the grid after save or cancel
            };

            DetailViewModel = detail;
        }
    }
}

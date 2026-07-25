using ErpSample.Common;

namespace ErpSample.Modules.Invoicing
{
    public class InvoiceShellViewModel : ObservableObject
    {
        private readonly IInvoiceRepository _repository;
        private InvoiceDetailViewModel _detailViewModel;

        public InvoiceListViewModel ListViewModel { get; private set; }

        public InvoiceDetailViewModel DetailViewModel
        {
            get { return _detailViewModel; }
            private set { SetProperty(ref _detailViewModel, value, "DetailViewModel"); }
        }

        public InvoiceShellViewModel(IInvoiceRepository repository)
        {
            _repository = repository;
            ListViewModel = new InvoiceListViewModel(repository);
            ListViewModel.EditRequested += OnEditRequested;
        }

        private void OnEditRequested(InvoiceHeader invoice)
        {
            var isNew = invoice == null;
            var item = invoice ?? new InvoiceHeader();

            var detail = new InvoiceDetailViewModel(item, isNew, _repository);
            detail.Closed += async () =>
            {
                DetailViewModel = null;
                await ListViewModel.LoadAsync();
            };

            DetailViewModel = detail;
        }
    }
}

using System.Threading.Tasks;
using ErpSample.Common;

namespace ErpSample.Modules.Invoicing
{
    public class InvoiceDetailViewModel : DetailViewModelBase<InvoiceHeader>
    {
        private readonly IInvoiceRepository _repository;

        public InvoiceDetailViewModel(InvoiceHeader item, bool isNew, IInvoiceRepository repository)
            : base(item, isNew)
        {
            _repository = repository;

            AddLineCommand = new RelayCommand(_ => Item.Lines.Add(new InvoiceLine()));
            RemoveLineCommand = new RelayCommand(
                line => { if (line is InvoiceLine l) Item.Lines.Remove(l); },
                line => line is InvoiceLine);
        }

        public RelayCommand AddLineCommand { get; }
        public RelayCommand RemoveLineCommand { get; }

        protected override Task PersistAsync(InvoiceHeader item) => _repository.SaveAsync(item);
    }
}

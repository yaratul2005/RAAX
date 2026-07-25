using System;
using System.Windows.Input;

namespace ErpSample.Common
{
    /// <summary>
    /// Generic ICommand so buttons/menu items/grid double-clicks bind straight to a
    /// ViewModel method instead of a code-behind click handler. Reused by every module -
    /// you should never need to write a new ICommand class per screen.
    /// </summary>
    public class RelayCommand : ICommand
    {
        private readonly Action<object?> _execute;
        private readonly Predicate<object?>? _canExecute;

        public RelayCommand(Action<object?> execute, Predicate<object?>? canExecute = null)
        {
            _execute = execute ?? throw new ArgumentNullException(nameof(execute));
            _canExecute = canExecute;
        }

        public bool CanExecute(object? parameter) => _canExecute?.Invoke(parameter) ?? true;

        public void Execute(object? parameter) => _execute(parameter);

        public event EventHandler? CanExecuteChanged
        {
            add => CommandManager.RequerySuggested += value;
            remove => CommandManager.RequerySuggested -= value;
        }

        /// <summary>
        /// Call after something that might flip a CanExecute result - e.g. after Save
        /// completes, to re-check whether the Save button should still be enabled.
        /// </summary>
        public static void RaiseCanExecuteChanged() => CommandManager.InvalidateRequerySuggested();
    }
}

using MiMercado.Api.Models;
using MiMercado.Api.Repositories;

namespace MiMercado.Api.Services
{
    public interface IProductService
    {
        Task<IEnumerable<Product>> GetAll();
        Task<Product?> Get(int id);
        Task<int> Create(Product p);
        Task<bool> Update(Product p);
        Task<bool> Delete(int id);
    }

    public class ProductService : IProductService
    {
        private readonly IProductRepository _repo;
        public ProductService(IProductRepository repo) => _repo = repo;

        public Task<IEnumerable<Product>> GetAll() => _repo.GetAllAsync();
        public Task<Product?> Get(int id) => _repo.GetByIdAsync(id);
        public Task<int> Create(Product p) => _repo.CreateAsync(p);
        public Task<bool> Update(Product p) => _repo.UpdateAsync(p);
        public Task<bool> Delete(int id) => _repo.DeleteAsync(id);
    }
}

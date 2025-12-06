using Microsoft.AspNetCore.Builder;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using Microsoft.Extensions.Configuration;
using Microsoft.OpenApi.Models;
using MySqlConnector;
using System.Data;
using Dapper;
using MiMercado.Api.Repositories;
using MiMercado.Api.Services;

var builder = WebApplication.CreateBuilder(args);

// Configuration
builder.Configuration.AddEnvironmentVariables();

// Add services
builder.Services.AddControllers();
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen(c =>
{
    c.SwaggerDoc("v1", new OpenApiInfo { Title = "MiMercado API", Version = "v0.5" });
});

// IDbConnection factory using connection string from appsettings or env var
var conn = builder.Configuration.GetConnectionString("Default") ?? Environment.GetEnvironmentVariable("ASPNETCORE_ConnectionStrings__Default");
builder.Services.AddTransient<IDbConnection>(_ => new MySqlConnection(conn ?? "Server=localhost;Database=db_sys_universities;User=root;Password=YOUR_PASSWORD;"));

// DI layers
builder.Services.AddScoped<IProductRepository, ProductRepository>();
builder.Services.AddScoped<IProductService, ProductService>();

var app = builder.Build();

// Middleware
if (app.Environment.IsDevelopment())
{
    app.UseDeveloperExceptionPage();
}
app.UseSwagger();
app.UseSwaggerUI(c => c.SwaggerEndpoint("/swagger/v1/swagger.json", "MiMercado API v0.5"));

app.UseRouting();
app.UseAuthorization();
app.MapControllers();

app.Run();
